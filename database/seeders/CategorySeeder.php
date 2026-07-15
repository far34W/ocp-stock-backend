<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            ['name' => 'I&C poste et convoyeur', 'description' => 'Instrumentation & contrôle poste et convoyeur'],
            ['name' => 'Électrique HTA',         'description' => 'Équipements haute tension'],
            ['name' => 'Moteurs',                 'description' => 'Moteurs électriques divers'],
            ['name' => 'Automatisme',             'description' => 'Automates programmables et variateurs'],
            ['name' => 'Câblage',                 'description' => 'Câbles et accessoires de câblage'],
        ];

        foreach ($categories as $cat) {
            Category::updateOrCreate(['name' => $cat['name']], $cat);
        }
    }
}
