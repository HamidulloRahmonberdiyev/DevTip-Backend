<?php

namespace App\Modules\Progress\Models;

use App\Modules\Level\Models\Level;
use App\Modules\Technology\Models\Skill;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SkillProgress extends Model
{
    protected $table = 'skill_progress';

    protected $fillable = [
        'user_id',
        'skill_id',
        'total_questions',
        'average_score',
        'level_id',
        'last_practiced_at',
    ];

    protected function casts(): array
    {
        return [
            'average_score' => 'decimal:2',
            'last_practiced_at' => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function skill(): BelongsTo
    {
        return $this->belongsTo(Skill::class);
    }

    public function level(): BelongsTo
    {
        return $this->belongsTo(Level::class);
    }
}
