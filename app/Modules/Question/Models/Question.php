<?php

namespace App\Modules\Question\Models;

use App\Modules\Interview\Models\UserAnswer;
use App\Modules\Ai\Models\AiRequest;
use App\Modules\Level\Models\Level;
use App\Modules\Tag\Models\Tag;
use App\Modules\Technology\Models\Skill;
use App\Modules\Technology\Models\Technology;
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
        'type',
        'title',
        'question',
        'answer',
        'expected_keywords',
    ];

    protected function casts(): array
    {
        return [
            'title' => 'array',       // ["uz" => "...", "ru" => "...", "en" => "..."]
            'question' => 'array',
            'answer' => 'array',
            'expected_keywords' => 'array',
        ];
    }

    /**
     * Berilgan til uchun title matnini qaytaradi.
     */
    public function getTitle(string $locale = 'uz'): string
    {
        return $this->title[$locale] ?? $this->title['en'] ?? (string) reset($this->title);
    }

    /**
     * Berilgan til uchun question matnini qaytaradi.
     */
    public function getQuestion(string $locale = 'uz'): string
    {
        return $this->question[$locale] ?? $this->question['en'] ?? (string) reset($this->question);
    }

    /**
     * Berilgan til uchun answer matnini qaytaradi.
     */
    public function getAnswer(string $locale = 'uz'): string
    {
        return $this->answer[$locale] ?? $this->answer['en'] ?? (string) reset($this->answer);
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
}
