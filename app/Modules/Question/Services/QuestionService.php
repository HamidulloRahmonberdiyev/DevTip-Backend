<?php

namespace App\Modules\Question\Services;

use App\Models\User;
use App\Modules\Interview\Models\InterviewSession;
use App\Modules\Question\Models\Question;
use App\Modules\Question\Models\QuestionRating;
use App\Modules\Question\Repositories\QuestionRepository;
use Illuminate\Support\Collection;

final class QuestionService
{
    public function __construct(
        private QuestionRepository $repository,
    ) {}

    /**
     * Quiz uchun savollarni qaytaradi. Userni oxirgi natijasidagi savollarni olmaydi.
     * Savollar olinganda InterviewSession (active) yaratiladi.
     *
     * @return array{questions: Collection<int, Question>, count: int, session: InterviewSession}
     */
    public function getQuestionsForQuiz(
        int $levelId,
        int $langId,
        int $technologyId,
        int $limit,
        ?User $user = null,
    ): array {
        $excludeIds = $this->getLastSessionQuestionIds($user);

        $questions = $this->repository->getRandomForQuiz(
            $levelId,
            $langId,
            $technologyId,
            $limit,
            $excludeIds,
        );

        if ($questions->isEmpty() && $excludeIds !== []) {
            $questions = $this->repository->getRandomForQuiz(
                $levelId,
                $langId,
                $technologyId,
                $limit,
                [],
            );
        }

        $session = null;
        if ($user !== null && $questions->isNotEmpty()) {
            $questionIds = $questions->pluck('id')->values()->all();
            $session = InterviewSession::create([
                'user_id' => $user->id,
                'status' => InterviewSession::STATUS_ACTIVE,
                'started_at' => now(),
                'completed_at' => null,
                'total_questions' => $questions->count(),
                'answered_questions' => 0,
                'average_score' => null,
                'level_id' => $levelId,
                'technology_id' => $technologyId,
                'lang_id' => $langId,
                'question_ids' => $questionIds,
            ]);
        }

        return [
            'questions' => $questions,
            'count' => $questions->count(),
            'session' => $session,
        ];
    }

    /**
     * Userni oxirgi session idagi savol ID larini qaytaradi.
     *
     * @return array<int, int>
     */
    private function getLastSessionQuestionIds(?User $user): array
    {
        if ($user === null) {
            return [];
        }

        $questionIds = InterviewSession::query()
            ->where('user_id', $user->id)
            ->where('status', InterviewSession::STATUS_COMPLETED)
            ->whereNotNull('question_ids')
            ->latest()
            ->value('question_ids');

        if (! is_array($questionIds)) {
            return [];
        }

        $ids = array_filter(array_map('intval', $questionIds), fn(int $id) => $id > 0);

        return array_values($ids);
    }

    /**
     * Savolni baholaydi. Bir foydalanuvchi bir savolga faqat bitta baho beradi (yangilansa bo'ladi).
     *
     * @return array{rating: float, rating_count: int}
     */
    public function rateQuestion(Question $question, User $user, int $stars): array
    {
        QuestionRating::updateOrCreate(
            [
                'user_id' => $user->id,
                'question_id' => $question->id,
            ],
            ['stars' => $stars]
        );

        $this->recalculateQuestionRating($question);

        return [
            'rating' => (float) $question->fresh()->rating,
            'rating_count' => (int) $question->fresh()->rating_count,
        ];
    }

    private function recalculateQuestionRating(Question $question): void
    {
        $stats = $question->ratings()
            ->selectRaw('AVG(stars) as avg, COUNT(*) as cnt')
            ->first();

        $question->update([
            'rating' => round((float) $stats->avg, 2),
            'rating_count' => (int) $stats->cnt,
        ]);
    }
}
