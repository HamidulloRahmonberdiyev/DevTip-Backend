<?php

namespace App\Models;

use App\Modules\Question\Models\Question;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Language extends Model
{
    protected $fillable = ['code', 'name'];

    public function questions(): HasMany
    {
        return $this->hasMany(Question::class, 'lang_id');
    }
}
