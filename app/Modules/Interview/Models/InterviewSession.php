<?php

namespace App\Modules\Interview\Models;

use App\Modules\Level\Models\Level;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class InterviewSession extends Model
{
    public const STATUS_ACTIVE = 'active';
    public const STATUS_COMPLETED = 'completed';

    protected $table = 'interview_sessions';

    protected $fillable = [
        'user_id',
        'status',
        'started_at',
        'completed_at',
        'total_questions',
        'answered_questions',
        'average_score',
        'level_id',
    ];

    protected function casts(): array
    {
        return [
            'started_at' => 'datetime',
            'completed_at' => 'datetime',
            'average_score' => 'decimal:2',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function level(): BelongsTo
    {
        return $this->belongsTo(Level::class);
    }

    public function userAnswers(): HasMany
    {
        return $this->hasMany(UserAnswer::class, 'session_id');
    }
}
