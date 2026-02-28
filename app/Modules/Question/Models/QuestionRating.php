<?php

namespace App\Modules\Question\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class QuestionRating extends Model
{
    public const MIN_STARS = 1;
    public const MAX_STARS = 5;

    protected $fillable = ['user_id', 'question_id', 'stars'];

    protected function casts(): array
    {
        return [
            'stars' => 'integer',
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
