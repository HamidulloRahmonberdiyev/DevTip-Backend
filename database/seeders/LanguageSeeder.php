<?php

namespace Database\Seeders;

use App\Models\Language;
use Illuminate\Database\Seeder;

class LanguageSeeder extends Seeder
{
    public function run(): void
    {
        $languages = [
            ['code' => 'uz', 'name' => 'O\'zbekcha'],
            ['code' => 'ru', 'name' => 'Русский'],
            ['code' => 'en', 'name' => 'English'],
        ];

        foreach ($languages as $lang) {
            Language::firstOrCreate(
                ['code' => $lang['code']],
                ['name' => $lang['name']]
            );
        }
    }
}
