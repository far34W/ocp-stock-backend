<?php

namespace Database\Seeders;

use App\Models\Article;
use App\Models\Category;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class ArticleSeeder extends Seeder
{
    public function run(): void
    {
        $catId = fn (string $name) => Category::where('name', $name)->value('id');

        $articles = [
            [
                'name'           => 'A2-11',
                'reference'      => '434343',
                'ocp_code'       => '989898',
                'quantity'       => 4,
                'min_quantity'   => 2,
                'unit'           => 'pièce',
                'brand'          => 'Schneider',
                'nature'         => 'Conditionelle',
                'supplier'       => 'Mohamed',
                'article_status' => 'Nouveau',
                'category_name'  => 'I&C poste et convoyeur',
            ],
            [
                'name'           => 'A2-66',
                'reference'      => '333333',
                'ocp_code'       => '555555',
                'quantity'       => 1,
                'min_quantity'   => 2,
                'unit'           => 'pièce',
                'brand'          => 'Schneider',
                'nature'         => 'Systématique',
                'supplier'       => 'Salim',
                'article_status' => 'Nouveau',
                'category_name'  => 'I&C poste et convoyeur',
            ],
            [
                'name'           => 'A2-88',
                'reference'      => '999999',
                'ocp_code'       => '666666',
                'quantity'       => 4,
                'min_quantity'   => 2,
                'unit'           => 'm3',
                'brand'          => 'Schneider',
                'nature'         => 'Critique',
                'supplier'       => 'Najib',
                'article_status' => 'Nouveau',
                'category_name'  => 'I&C poste et convoyeur',
            ],
            [
                'name'           => 'A2-87',
                'reference'      => '12345678',
                'ocp_code'       => '7654321',
                'quantity'       => 5,
                'min_quantity'   => 2,
                'unit'           => 'pièce',
                'brand'          => 'Schneider',
                'nature'         => 'Conditionelle',
                'supplier'       => 'Ali',
                'article_status' => 'Ancien',
                'category_name'  => 'I&C poste et convoyeur',
            ],
        ];

        foreach ($articles as $data) {
            $categoryName = $data['category_name'];
            unset($data['category_name']);

            // Auto-generate barcode if not present
            $data['barcode']     = $data['barcode'] ?? 'PROD-' . now()->format('Ymd') . '-' . Str::upper(Str::random(6));
            $data['category_id'] = $catId($categoryName);

            Article::updateOrCreate(
                ['reference' => $data['reference']],
                $data
            );
        }
    }
}
