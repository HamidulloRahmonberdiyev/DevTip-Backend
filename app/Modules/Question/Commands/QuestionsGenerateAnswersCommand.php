<?php

namespace App\Modules\Question\Commands;

use App\Modules\Question\Models\Question;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;

final class QuestionsGenerateAnswersCommand extends Command
{
    protected $signature = 'questions:generate-answers
        {--only-empty : Faqat javobi bo\'sh savollarni yangilash}
        {--limit= : Maksimal savollar soni (masalan: 5)}
        {--dry-run : AI so\'rov yuboriladi lekin DB yangilanmaydi}';

    protected $description = 'Question ustunidagi savolni AI orqali javoblab, answer ustuniga HTML da yozadi (Ollama yoki Groq)';

    public function handle(): int
    {
        $driver = config('ai.driver');
        if (! in_array($driver, ['ollama', 'groq'], true)) {
            $this->error("AI_DRIVER 'ollama' yoki 'groq' bo'lishi kerak. Hozir: {$driver}");

            return self::FAILURE;
        }

        if ($driver === 'groq' && empty(config('ai.groq.api_key'))) {
            $this->error("Groq ishlatish uchun .env da GROQ_API_KEY belgilang (bepul key: console.groq.com).");

            return self::FAILURE;
        }

        $query = Question::query()->whereNotNull('question')->where('question', '!=', '');

        if ($this->option('only-empty')) {
            $query->where(function ($q) {
                $q->whereNull('answer')->orWhere('answer', '');
            });
        }

        $limit = $this->option('limit');
        if ($limit !== null && is_numeric($limit)) {
            $query->limit((int) $limit);
        }

        $questions = $query->get();

        if ($questions->isEmpty()) {
            $this->info('Ishlov beriladigan savol topilmadi.');

            return self::SUCCESS;
        }

        $dryRun = $this->option('dry-run');
        if ($dryRun) {
            $this->warn('Dry run – javoblar bazaga yozilmaydi.');
        }

        $this->info(sprintf('AI: %s. Jami %d ta savol.', $driver, $questions->count()));

        $bar = $this->output->createProgressBar($questions->count());
        $bar->start();

        $updated = 0;
        $errors = [];

        foreach ($questions as $question) {
            try {
                $answerText = $this->askAi($question->question, $driver);
                if ($answerText === null) {
                    $errors[] = "ID {$question->id}: AI javob qaytarmadi.";
                    $bar->advance();
                    continue;
                }
                $answerHtml = $this->formatAnswerToHtml(trim($answerText));
                if (! $dryRun) {
                    $question->update(['answer' => $answerHtml]);
                }
                $updated++;
            } catch (\Throwable $e) {
                $errors[] = sprintf('ID %d: %s', $question->id, $e->getMessage());
            }
            $bar->advance();
        }

        $bar->finish();
        $this->newLine();

        if ($errors !== []) {
            $this->warn('Xatolar:');
            foreach (array_slice($errors, 0, 10) as $err) {
                $this->line('  ' . $err);
            }
            if (count($errors) > 10) {
                $this->line('  ... va yana ' . (count($errors) - 10) . ' ta.');
            }
        }

        $this->info(sprintf(
            'Tayyor: %d ta javob %s.',
            $updated,
            $dryRun ? 'olingani (saqlanmadi)' : 'yozildi'
        ));

        return empty($errors) ? self::SUCCESS : self::FAILURE;
    }

    private function askAi(string $questionText, string $driver): ?string
    {
        $prompt = "Quyidagi savolga qisqa, aniq va foydali javob bering. Faqat javob matnini yozing, sarlavha yoki \"Javob:\" kabi so'zlarsiz.\n\nSavol: " . trim($questionText);

        if ($driver === 'ollama') {
            return $this->askOllama($prompt);
        }

        if ($driver === 'groq') {
            return $this->askGroq($prompt);
        }

        return null;
    }

    private function askOllama(string $prompt): ?string
    {
        $url = rtrim(config('ai.ollama.url'), '/') . '/api/chat';
        $timeout = config('ai.ollama.timeout', 120);

        $response = Http::timeout($timeout)
            ->post($url, [
                'model' => config('ai.ollama.model'),
                'messages' => [
                    ['role' => 'user', 'content' => $prompt],
                ],
                'stream' => false,
            ]);

        if (! $response->successful()) {
            throw new \RuntimeException(
                'Ollama javob bermadi. Ollama ishlayaptimi? (ollama serve). ' . $response->body()
            );
        }

        $data = $response->json();
        $content = $data['message']['content'] ?? null;

        return is_string($content) ? $content : null;
    }

    private function askGroq(string $prompt): ?string
    {
        $url = 'https://api.groq.com/openai/v1/chat/completions';
        $timeout = config('ai.groq.timeout', 60);

        $response = Http::withToken(config('ai.groq.api_key'))
            ->timeout($timeout)
            ->post($url, [
                'model' => config('ai.groq.model'),
                'messages' => [
                    ['role' => 'user', 'content' => $prompt],
                ],
                'max_tokens' => 1024,
            ]);

        if (! $response->successful()) {
            $body = $response->json();
            $msg = $body['error']['message'] ?? $response->body();
            throw new \RuntimeException('Groq API xato: ' . $msg);
        }

        $data = $response->json();
        $content = $data['choices'][0]['message']['content'] ?? null;

        return is_string($content) ? $content : null;
    }

    private function formatAnswerToHtml(string $text): string
    {
        $text = trim($text);
        if ($text === '') {
            return '';
        }

        $escaped = htmlspecialchars($text, ENT_QUOTES | ENT_HTML5, 'UTF-8');
        $paragraphs = preg_split('/\n\s*\n/', $escaped, -1, PREG_SPLIT_NO_EMPTY);
        $blocks = [];

        foreach ($paragraphs as $para) {
            $para = trim($para);
            if ($para === '') {
                continue;
            }
            $withBr = str_replace("\n", '<br>', $para);
            $blocks[] = '<p class="mb-3 leading-relaxed text-gray-700 dark:text-gray-300">' . $withBr . '</p>';
        }

        if ($blocks === []) {
            $withBr = str_replace("\n", '<br>', $escaped);
            $blocks[] = '<p class="mb-3 leading-relaxed text-gray-700 dark:text-gray-300">' . $withBr . '</p>';
        }

        return '<div class="space-y-3">' . implode('', $blocks) . '</div>';
    }
}
