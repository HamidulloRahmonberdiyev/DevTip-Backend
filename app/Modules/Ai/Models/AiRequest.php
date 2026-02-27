<?php

namespace App\Modules\Ai\Models;

use App\Modules\Question\Models\Question;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AiRequest extends Model
{
    protected $table = 'ai_requests';

    public const UPDATED_AT = null;

    protected $fillable = [
        'user_id',
        'question_id',
        'prompt',
        'response',
        'tokens_used',
        'response_time_ms',
    ];

    protected function casts(): array
    {
        return [
            'created_at' => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function question(): BelongsTo
    {
        return $this->belongsTo(Question::class);
    }
}
