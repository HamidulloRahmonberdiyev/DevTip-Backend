<?php

namespace App\Modules\Question\Http\Resources;

use App\Modules\Question\Models\Question;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin Question */
final class QuestionResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'technology_id' => $this->technology_id,
            'skill_id' => $this->skill_id,
            'lang_id' => $this->lang_id,
            'type' => $this->type,
            'title' => $this->title,
            'question' => $this->question,
            'answer' => $this->answer,
            'rating' => $this->rating,
            'rating_count' => $this->rating_count,
            'views' => $this->views,
        ];
    }
}
