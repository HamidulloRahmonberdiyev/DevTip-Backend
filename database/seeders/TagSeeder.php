<?php

namespace Database\Seeders;

use App\Modules\Tag\Models\Tag;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class TagSeeder extends Seeder
{
    public function run(): void
    {
        $tags = [
            'Algoritmlar',
            'Ma\'lumotlar tuzilmasi',
            'OOP',
            'SQL',
            'REST API',
            'Security',
            'Performance',
            'Design Patterns',
            'Testing',
            'Git',
            'Linux',
            'Docker',
        ];

        foreach ($tags as $name) {
            Tag::updateOrCreate(
                ['slug' => Str::slug($name)],
                ['name' => $name]
            );
        }
    }
}
