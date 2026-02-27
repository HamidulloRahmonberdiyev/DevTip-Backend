<?php

namespace App\Modules\Technology\Models;

use App\Modules\Progress\Models\SkillProgress;
use App\Modules\Question\Models\Question;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Skill extends Model
{
    protected $fillable = [
        'technology_id',
        'name',
        'slug',
        'description',
        'image',
    ];

    public function technology(): BelongsTo
    {
        return $this->belongsTo(Technology::class);
    }

    public function questions(): HasMany
    {
        return $this->hasMany(Question::class);
    }

    public function skillProgress(): HasMany
    {
        return $this->hasMany(SkillProgress::class);
    }
}
