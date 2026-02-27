<?php

namespace App\Modules\Question\Commands;

use App\Modules\Level\Models\Level;
use App\Modules\Question\Models\Question;
use App\Modules\Tag\Models\Tag;
use App\Modules\Technology\Models\Skill;
use App\Modules\Technology\Models\Technology;
use Illuminate\Console\Command;
use Illuminate\Support\Str;

class QuestionsImportCommand extends Command
{
    protected $signature = 'questions:import {file : JSON fayl nomi (masalan: php-oop.json)}';

    protected $description = 'JSON fayldan savollarni import qiladi';

    public function handle(): int
    {
        $fileName = $this->argument('file');

        $path = base_path($fileName);
        if (! file_exists($path)) {
            $path = base_path('storage/app/questions/' . $fileName);
        }
        if (! file_exists($path)) {
            $this->error("Fayl topilmadi: {$fileName}");
            return self::FAILURE;
        }

        $data = json_decode(file_get_contents($path), true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            $this->error('JSON xato: ' . json_last_error_msg());
            return self::FAILURE;
        }

        $items = $data['questions'] ?? $data;
        if (! is_array($items)) {
            $this->error('JSON da "questions" massivi bo\'lishi kerak yoki to\'g\'ridan-to\'g\'ri massiv.');
            return self::FAILURE;
        }

        $bar = $this->output->createProgressBar(count($items));
        $bar->start();

        $imported = 0;
        foreach ($items as $item) {
            try {
                $this->importQuestion($item);
                $imported++;
            } catch (\Throwable $e) {
                $this->newLine();
                $this->warn("Xato (qator " . ($imported + 1) . "): " . $e->getMessage());
            }
            $bar->advance();
        }

        $bar->finish();
        $this->newLine();
        $this->info("{$imported} ta savol import qilindi.");

        return self::SUCCESS;
    }

    private function importQuestion(array $item): void
    {
        $technology = $this->resolveTechnology($item);
        $skill = $this->resolveSkill($item, $technology);

        $question = Question::create([
            'technology_id' => $technology->id,
            'skill_id' => $skill?->id,
            'type' => $this->normalizeType($item['type'] ?? 'text'),
            'title' => $this->normalizeTranslations($item['title'] ?? []),
            'question' => $this->normalizeTranslations($item['question'] ?? []),
            'answer' => $this->normalizeTranslations($item['answer'] ?? []),
            'expected_keywords' => $item['expected_keywords'] ?? null,
        ]);

        if (! empty($item['levels'])) {
            $levelIds = Level::whereIn('slug', array_map(fn ($s) => Str::slug($s), (array) $item['levels']))
                ->pluck('id')
                ->toArray();
            $question->levels()->sync($levelIds);
        }

        if (! empty($item['tags'])) {
            $tagIds = Tag::whereIn('slug', array_map(fn ($s) => Str::slug($s), (array) $item['tags']))
                ->pluck('id')
                ->toArray();
            $question->tags()->sync($tagIds);
        }
    }

    private function resolveTechnology(array $item): Technology
    {
        if (isset($item['technology_id'])) {
            return Technology::findOrFail($item['technology_id']);
        }
        $slug = Str::slug($item['technology'] ?? $item['technology_slug'] ?? '');
        return Technology::where('slug', $slug)->firstOrFail();
    }

    private function resolveSkill(?array $item, Technology $technology): ?Skill
    {
        if (empty($item['skill']) && empty($item['skill_slug']) && empty($item['skill_id'])) {
            return null;
        }
        if (isset($item['skill_id'])) {
            return Skill::where('technology_id', $technology->id)->findOrFail($item['skill_id']);
        }
        $slug = Str::slug($item['skill'] ?? $item['skill_slug'] ?? '');
        return Skill::where('technology_id', $technology->id)->where('slug', $slug)->first();
    }

    private function normalizeType(string $type): string
    {
        $type = strtolower($type);
        return in_array($type, ['text', 'coding', 'scenario']) ? $type : 'text';
    }

    private function normalizeTranslations(mixed $value): array
    {
        if (is_string($value)) {
            return ['en' => $value];
        }
        if (is_array($value)) {
            return $value;
        }
        return ['en' => ''];
    }
}
