<?php

namespace Database\Seeders;

use App\Models\Language;
use Illuminate\Database\Seeder;

class LanguageSeeder extends Seeder
{
    public function run(): void
    {
        // ID: 1 = en, 2 = ru, 3 = uz
        $languages = [
            1 => ['code' => 'en', 'name' => 'English'],
            2 => ['code' => 'ru', 'name' => 'Русский'],
            3 => ['code' => 'uz', 'name' => 'O\'zbekcha'],
        ];

        foreach ($languages as $id => $lang) {
            Language::updateOrCreate(
                ['id' => $id],
                ['code' => $lang['code'], 'name' => $lang['name']]
            );
        }
    }
}
