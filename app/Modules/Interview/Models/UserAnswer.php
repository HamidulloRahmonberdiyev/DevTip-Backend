<?php

namespace App\Modules\Interview\Models;

use App\Modules\Question\Models\Question;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class UserAnswer extends Model
{
    protected $table = 'user_answers';

    protected $fillable = [
        'session_id',
        'user_id',
        'question_id',
        'answer',
        'ai_score',
        'answered_at',
    ];

    protected function casts(): array
    {
        return [
            'answered_at' => 'datetime',
            'ai_score' => 'decimal:2',
        ];
    }

    public function session(): BelongsTo
    {
        return $this->belongsTo(InterviewSession::class, 'session_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function question(): BelongsTo
    {
        return $this->belongsTo(Question::class);
    }

    public function aiEvaluation(): HasOne
    {
        return $this->hasOne(AiEvaluation::class);
    }
}
