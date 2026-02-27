<?php

namespace App\Modules\Level\Models;

use App\Models\User;
use App\Modules\Interview\Models\InterviewSession;
use App\Modules\Progress\Models\SkillProgress;
use App\Modules\Question\Models\Question;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Level extends Model
{
    protected $fillable = [
        'name',
        'slug',
        'rank',
        'description',
    ];

    public function users(): HasMany
    {
        return $this->hasMany(User::class, 'current_level_id');
    }

    public function questions(): BelongsToMany
    {
        return $this->belongsToMany(Question::class, 'question_level')
            ->withTimestamps();
    }

    public function interviewSessions(): HasMany
    {
        return $this->hasMany(InterviewSession::class);
    }

    public function skillProgress(): HasMany
    {
        return $this->hasMany(SkillProgress::class);
    }
}
