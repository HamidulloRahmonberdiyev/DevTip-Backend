<?php

namespace App\Modules\Interview\Services;

use App\Models\User;
use App\Modules\Interview\Models\InterviewSession;
use App\Modules\Interview\Models\UserAnswer;
use Illuminate\Support\Facades\DB;

final class InterviewSessionService
{
    /**
     * Mavjud session ni yakunlaydi va qiymatlarini yangilaydi.
     */
    public function complete(User $user, int $sessionId, array $answers = []): InterviewSession
    {
        return DB::transaction(function () use ($user, $sessionId, $answers) {
            $session = InterviewSession::where('id', $sessionId)
                ->where('user_id', $user->id)
                ->where('status', InterviewSession::STATUS_ACTIVE)
                ->firstOrFail();

            $now = now();
            $answeredCount = count($answers);
            $scores = array_values(array_filter(
                array_map(fn ($a) => isset($a['score']) ? (float) $a['score'] : null, $answers),
                fn (?float $s) => $s !== null
            ));
            $averageScore = $scores !== []
                ? round(array_sum($scores) / count($scores), 2)
                : null;

            $session->update([
                'status' => InterviewSession::STATUS_COMPLETED,
                'completed_at' => $now,
                'answered_questions' => $answeredCount,
                'average_score' => $averageScore,
            ]);

            foreach ($answers as $item) {
                UserAnswer::create([
                    'session_id' => $session->id,
                    'user_id' => $user->id,
                    'question_id' => $item['question_id'],
                    'answer' => $item['answer'],
                    'ai_score' => $item['score'] ?? null,
                    'answered_at' => $now,
                ]);
            }

            return $session->fresh();
        });
    }
}
