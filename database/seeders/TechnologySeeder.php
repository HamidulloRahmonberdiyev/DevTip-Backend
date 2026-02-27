<?php

namespace Database\Seeders;

use App\Modules\Technology\Models\Technology;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class TechnologySeeder extends Seeder
{
    public function run(): void
    {
        $technologies = [
            [
                'name' => 'PHP',
                'description' => 'Server-side dasturlash tili. Laravel, Symfony asosida veb-ilovalar.',
            ],
            [
                'name' => 'Laravel',
                'description' => 'PHP framework — MVC, Eloquent, Blade, API.',
            ],
            [
                'name' => 'JavaScript',
                'description' => 'Frontend va Node.js orqali backend. React, Vue, Angular.',
            ],
            [
                'name' => 'React',
                'description' => 'Frontend framework. Komponent bazli arxitektura, JSX, state management.',
            ],
            [
                'name' => 'Python',
                'description' => 'Umumiy maqsadli til. Django, FastAPI, ML, avtomatlashtirish.',
            ],
            [
                'name' => 'Django',
                'description' => 'Python framework. MTV arxitekturasi, ORM, admin panel.',
            ],
            [
                'name' => 'MySQL',
                'description' => 'Relatsion ma\'lumotlar bazasi. SQL, indekslar, optimallashtirish.',
            ],
            [
                'name' => 'PostgreSQL',
                'description' => 'SQL, JSON, kengaytmalar, replikatsiya. Rivojlangan ma\'lumotlar bazasi.',
            ],
        ];

        foreach ($technologies as $item) {
            Technology::updateOrCreate(
                ['slug' => Str::slug($item['name'])],
                [
                    'name' => $item['name'],
                    'description' => $item['description'],
                ]
            );
        }
    }
}
