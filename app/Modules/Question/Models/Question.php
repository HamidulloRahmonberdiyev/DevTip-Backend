<?php

namespace App\Modules\Question\Models;

use App\Models\Language;
use App\Modules\Interview\Models\UserAnswer;
use App\Modules\Ai\Models\AiRequest;
use App\Modules\Level\Models\Level;
use App\Modules\Tag\Models\Tag;
use App\Modules\Technology\Models\Skill;
use App\Modules\Technology\Models\Technology;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Question extends Model
{
    public const TYPE_TEXT = 'text';
    public const TYPE_CODING = 'coding';
    public const TYPE_SCENARIO = 'scenario';

    protected $fillable = [
        'technology_id',
        'skill_id',
        'lang_id',
        'type',
        'title',
        'question',
        'answer',
        'expected_keywords',
        'rating',
        'rating_count',
        'views',
    ];

    protected function casts(): array
    {
        return [
            'expected_keywords' => 'array',
            'rating' => 'float',
            'rating_count' => 'integer',
            'views' => 'integer',
        ];
    }

    /**
     * Savollarni level, til va texnologiya bo‘yicha filtrlash.
     */
    public function scopeForQuiz(Builder $query, int $levelId, int $langId, int $technologyId): Builder
    {
        return $query
            ->where('lang_id', $langId)
            ->where('technology_id', $technologyId)
            ->whereHas('levels', fn (Builder $q) => $q->where('levels.id', $levelId));
    }

    public function language(): BelongsTo
    {
        return $this->belongsTo(Language::class, 'lang_id');
    }

    public function technology(): BelongsTo
    {
        return $this->belongsTo(Technology::class);
    }

    public function skill(): BelongsTo
    {
        return $this->belongsTo(Skill::class);
    }

    public function levels(): BelongsToMany
    {
        return $this->belongsToMany(Level::class, 'question_level')
            ->withTimestamps();
    }

    public function tags(): BelongsToMany
    {
        return $this->belongsToMany(Tag::class, 'question_tag')
            ->withTimestamps();
    }

    public function userAnswers(): HasMany
    {
        return $this->hasMany(UserAnswer::class);
    }

    public function aiRequests(): HasMany
    {
        return $this->hasMany(AiRequest::class);
    }

    public function ratings(): HasMany
    {
        return $this->hasMany(QuestionRating::class);
    }
}
