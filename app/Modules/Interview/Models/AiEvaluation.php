<?php

namespace App\Modules\Interview\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AiEvaluation extends Model
{
    protected $table = 'ai_evaluations';

    protected $fillable = [
        'user_answer_id',
        'score',
        'strengths',
        'weaknesses',
        'missing_keywords',
        'ai_answer',
        'confidence_score',
    ];

    protected function casts(): array
    {
        return [
            'score' => 'decimal:2',
            'confidence_score' => 'decimal:2',
            'missing_keywords' => 'array',
        ];
    }

    public function userAnswer(): BelongsTo
    {
        return $this->belongsTo(UserAnswer::class);
    }
}
