<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            ['name' => 'Pelatihan Dasar'],
            ['name' => 'Pelatihan Lanjutan'],
            ['name' => 'Kebersihan & Grooming'],
            ['name' => 'Sosialisasi'],
            ['name' => 'Kesehatan & Nutrisi'],
        ];

        foreach ($categories as $category) {
            Category::updateOrCreate(['name' => $category['name']], $category);
        }

        $this->command->info('✅ Category seeder selesai: ' . count($categories) . ' kategori ditambahkan');
    }
}
