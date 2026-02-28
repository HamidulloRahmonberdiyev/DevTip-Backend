<?php

namespace App\Modules\Interview\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

final class InterviewSessionResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'status' => $this->status,
            'started_at' => $this->started_at?->toIso8601String(),
            'completed_at' => $this->completed_at?->toIso8601String(),
            'total_questions' => $this->total_questions,
            'answered_questions' => $this->answered_questions,
            'average_score' => $this->average_score !== null ? (float) $this->average_score : null,
            'level_id' => $this->level_id,
            'technology_id' => $this->technology_id,
            'lang_id' => $this->lang_id,
            'question_ids' => $this->question_ids,
        ];
    }
}
