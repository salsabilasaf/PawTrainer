<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Tutorial;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        $basic = Category::updateOrCreate(['name' => 'Dasar']);
        $behavior = Category::updateOrCreate(['name' => 'Perilaku']);
        $tricks = Category::updateOrCreate(['name' => 'Trik']);

        Tutorial::updateOrCreate(
            ['title' => 'Mengenalkan Clicker Training'],
            [
                'category_id' => $basic->id,
                'content' => 'Mulai dengan bunyi clicker pendek lalu berikan reward kecil. Ulangi sampai kucing mengaitkan bunyi clicker dengan hadiah.',
                'difficulty' => 'beginner',
                'estimated_time' => 10,
                'youtube_url' => null,
                'image_url' => null,
            ]
        );

        Tutorial::updateOrCreate(
            ['title' => 'Melatih Kucing Datang Saat Dipanggil'],
            [
                'category_id' => $behavior->id,
                'content' => 'Panggil nama kucing dengan suara konsisten, beri reward saat ia mendekat, lalu tambah jarak secara bertahap.',
                'difficulty' => 'beginner',
                'estimated_time' => 15,
                'youtube_url' => null,
                'image_url' => null,
            ]
        );

        Tutorial::updateOrCreate(
            ['title' => 'High Five Sederhana'],
            [
                'category_id' => $tricks->id,
                'content' => 'Gunakan treat di dekat telapak tangan. Saat kucing menyentuh tangan, klik dan beri reward. Ulangi sampai gerakannya konsisten.',
                'difficulty' => 'intermediate',
                'estimated_time' => 20,
                'youtube_url' => null,
                'image_url' => null,
            ]
        );
    }
}
