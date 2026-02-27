<?php

namespace App\Modules\Technology\Models;

use App\Modules\Question\Models\Question;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Technology extends Model
{
    protected $fillable = [
        'name',
        'slug',
        'description',
        'image',
    ];

    public function skills(): HasMany
    {
        return $this->hasMany(Skill::class);
    }

    public function questions(): HasMany
    {
        return $this->hasMany(Question::class);
    }
}
