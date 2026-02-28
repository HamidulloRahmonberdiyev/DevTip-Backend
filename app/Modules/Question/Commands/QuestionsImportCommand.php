<?php

namespace App\Modules\Question\Commands;

use App\Models\Language;
use App\Modules\Level\Models\Level;
use App\Modules\Question\Models\Question;
use App\Modules\Tag\Models\Tag;
use App\Modules\Technology\Models\Skill;
use App\Modules\Technology\Models\Technology;
use Illuminate\Console\Command;
use Illuminate\Support\Str;

final class QuestionsImportCommand extends Command
{
    protected $signature = 'questions:import
        {file : JSON fayl (storage/app/questions/ yoki to\'liq yo\'l)}
        {--dry-run : Import qilmasdan tekshirish}';

    protected $description = 'JSON fayldan savollarni import qiladi (har til uchun alohida qator)';

    private const TYPE_ALLOWED = ['text', 'coding', 'scenario'];

    private const DEFAULT_LOCALE = 'en';

    public function handle(): int
    {
        $path = $this->resolvePath($this->argument('file'));
        if ($path === null) {
            return self::FAILURE;
        }

        $items = $this->loadItems($path);
        if ($items === null) {
            return self::FAILURE;
        }

        $dryRun = $this->option('dry-run');
        if ($dryRun) {
            $this->warn('Dry run – hech narsa saqlanmaydi.');
        }

        $bar = $this->output->createProgressBar(count($items));
        $bar->start();

        $imported = 0;
        $errors = [];

        foreach ($items as $index => $item) {
            try {
                if (! $dryRun) {
                    $this->importItem($item);
                }
                $imported++;
            } catch (\Throwable $e) {
                $errors[] = sprintf('#%d: %s', $index + 1, $e->getMessage());
            }
            $bar->advance();
        }

        $bar->finish();
        $this->newLine();

        if ($errors !== []) {
            $this->error('Xatolar:');
            foreach ($errors as $err) {
                $this->line('  ' . $err);
            }
        }

        $this->info(sprintf('%d ta savol %s.', $imported, $dryRun ? 'tekshirildi' : 'import qilindi'));

        return self::SUCCESS;
    }

    private function resolvePath(string $file): ?string
    {
        $candidates = [
            $file,
            base_path($file),
            base_path('storage/app/questions/' . $file),
        ];

        foreach ($candidates as $path) {
            if (file_exists($path)) {
                return $path;
            }
        }

        $this->error("Fayl topilmadi: {$file}");

        return null;
    }

    /**
     * @return array<int, array<string, mixed>>|null
     */
    private function loadItems(string $path): ?array
    {
        $content = file_get_contents($path);
        $data = json_decode($content, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            $this->error('JSON xato: ' . json_last_error_msg());

            return null;
        }

        $items = $data['questions'] ?? $data;
        if (! is_array($items)) {
            $this->error('JSON da "questions" massivi yoki to\'g\'ridan-to\'g\'ri massiv bo\'lishi kerak.');

            return null;
        }

        return $items;
    }

    /**
     * @param  array<string, mixed>  $item
     */
    private function importItem(array $item): void
    {
        $technology = $this->resolveTechnology($item);
        $skill = $this->resolveSkill($item, $technology);
        $translations = $this->extractTranslations($item);
        $locales = $this->collectLocales($translations);

        $levelIds = $this->resolveLevelIds($item);
        $tagIds = $this->resolveTagIds($item);

        foreach ($locales as $code) {
            $lang = Language::firstOrCreate(['code' => $code], ['name' => $code]);

            $question = Question::create([
                'technology_id' => $technology->id,
                'skill_id' => $skill?->id,
                'lang_id' => $lang->id,
                'type' => $this->normalizeType($item['type'] ?? 'text'),
                'title' => $translations['title'][$code] ?? $translations['title'][self::DEFAULT_LOCALE] ?? null,
                'question' => $translations['question'][$code] ?? $translations['question'][self::DEFAULT_LOCALE] ?? '',
                'answer' => $translations['answer'][$code] ?? $translations['answer'][self::DEFAULT_LOCALE] ?? null,
                'expected_keywords' => $item['expected_keywords'] ?? null,
            ]);

            $question->levels()->sync($levelIds);
            $question->tags()->sync($tagIds);
        }
    }

    /**
     * @param  array<string, mixed>  $item
     * @return array{title: array<string, string>, question: array<string, string>, answer: array<string, string|null>}
     */
    private function extractTranslations(array $item): array
    {
        $normalize = fn(mixed $v): array => $this->normalizeTranslations($v);

        return [
            'title' => $normalize($item['title'] ?? []),
            'question' => $normalize($item['question'] ?? []),
            'answer' => $normalize($item['answer'] ?? []),
        ];
    }

    /**
     * @param  array{title: array<string, string>, question: array<string, string>, answer: array<string, string|null>}  $translations
     * @return array<int, string>
     */
    private function collectLocales(array $translations): array
    {
        $keys = array_merge(
            array_keys($translations['title']),
            array_keys($translations['question']),
            array_keys($translations['answer'])
        );

        $locales = array_unique(array_filter($keys));
        if ($locales === []) {
            return [self::DEFAULT_LOCALE];
        }

        return array_values($locales);
    }

    /**
     * @param  array<string, mixed>  $item
     */
    private function resolveTechnology(array $item): Technology
    {
        if (isset($item['technology_id'])) {
            return Technology::findOrFail($item['technology_id']);
        }

        $slug = Str::slug($item['technology'] ?? $item['technology_slug'] ?? '');

        return Technology::where('slug', $slug)->firstOrFail();
    }

    /**
     * @param  array<string, mixed>  $item
     */
    private function resolveSkill(array $item, Technology $technology): ?Skill
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

    /**
     * @param  array<string, mixed>  $item
     * @return array<int, int>
     */
    private function resolveLevelIds(array $item): array
    {
        $slugs = $item['levels'] ?? [];
        if (! is_array($slugs) || $slugs === []) {
            return [];
        }

        return Level::whereIn('slug', array_map(fn($s) => Str::slug((string) $s), $slugs))
            ->pluck('id')
            ->toArray();
    }

    /**
     * @param  array<string, mixed>  $item
     * @return array<int, int>
     */
    private function resolveTagIds(array $item): array
    {
        $slugs = $item['tags'] ?? [];
        if (! is_array($slugs) || $slugs === []) {
            return [];
        }

        return Tag::whereIn('slug', array_map(fn($s) => Str::slug((string) $s), $slugs))
            ->pluck('id')
            ->toArray();
    }

    private function normalizeType(string $type): string
    {
        $type = strtolower(trim($type));

        return in_array($type, self::TYPE_ALLOWED, true) ? $type : 'text';
    }

    /**
     * @return array<string, string|null>
     */
    private function normalizeTranslations(mixed $value): array
    {
        if (is_string($value)) {
            return [self::DEFAULT_LOCALE => $value];
        }
        if (is_array($value)) {
            return $value;
        }

        return [self::DEFAULT_LOCALE => ''];
    }
}
