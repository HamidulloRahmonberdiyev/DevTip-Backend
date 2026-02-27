<?php

namespace Database\Seeders;

use App\Modules\Level\Models\Level;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class LevelSeeder extends Seeder
{
    public function run(): void
    {
        $levels = [
            [
                'name' => 'Junior',
                'rank' => 1,
                'description' => 'Boshlang\'ich daraja — asosiy tushunchalar va oddiy vazifalar.',
            ],
            [
                'name' => 'Middle',
                'rank' => 2,
                'description' => 'O\'rta daraja — mustaqil ishlash, murakkab vazifalar.',
            ],
            [
                'name' => 'Senior',
                'rank' => 3,
                'description' => 'Yuqori daraja — arxitektura, mentorschilik, murakkab tizimlar.',
            ],
        ];

        foreach ($levels as $item) {
            Level::updateOrCreate(
                ['slug' => Str::slug($item['name'])],
                [
                    'name' => $item['name'],
                    'rank' => $item['rank'],
                    'description' => $item['description'],
                ]
            );
        }
    }
}
