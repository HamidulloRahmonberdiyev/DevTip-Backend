<?php

namespace Database\Seeders;

use App\Modules\Technology\Models\Skill;
use App\Modules\Technology\Models\Technology;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class SkillSeeder extends Seeder
{
    public function run(): void
    {
        $skillsByTechnology = [
            'PHP' => [
                'OOP va namespace',
                'Composer va autoload',
                'PDO va ma\'lumotlar bazasi',
                'Session va Cookie',
                'Xavfsizlik (SQL injection, XSS, CSRF)',
                'REST API yozish',
            ],
            'Laravel' => [
                'Eloquent ORM',
                'Migration va Seeder',
                'Blade templating',
                'Routing va Middleware',
                'Queue va Job',
                'Laravel Sanctum / Passport',
            ],
            'JavaScript' => [
                'ES6+ (arrow, destructuring, async/await)',
                'DOM va Event',
                'Promises va async',
                'Fetch API / Axios',
                'Single Page Application (SPA)',
            ],
            'React' => [
                'JSX va komponentlar',
                'State va Props',
                'Lifecycle metodlari',
                'Hooks (useState, useEffect)',
                'Context API',
                'Redux / MobX',
            ],
            'Python' => [
                'OOP va decorators',
                'Virtualenv va pip',
                'List comprehension',
                'Asyncio',
                'Testing (pytest)',
            ],
            'Django' => [
                'MTV arxitekturasi',
                'ORM va QuerySet',
                'Admin panel yaratish',
                'Formalar va validatsiya',
                'Authentication va authorization',
            ],
            'MySQL' => [
                'SELECT, JOIN, subquery',
                'Indekslar va EXPLAIN',
                'Transaction va ACID',
                'Normalizatsiya',
                'Optimizatsiya',
            ],
            'PostgreSQL' => [
                'SELECT, JOIN, subquery',
                'Indekslar va EXPLAIN',
                'Transaction va ACID',
                'Normalizatsiya',
                'Optimizatsiya',
                'JSONB va kengaytmalar',
            ],
        ];

        foreach ($skillsByTechnology as $techName => $skillNames) {
            $technology = Technology::where('slug', Str::slug($techName))->first();
            if (! $technology) {
                continue;
            }

            foreach ($skillNames as $name) {
                Skill::updateOrCreate(
                    [
                        'technology_id' => $technology->id,
                        'slug' => Str::slug($name),
                    ],
                    [
                        'name' => $name,
                        'description' => null,
                    ]
                );
            }
        }
    }
}
