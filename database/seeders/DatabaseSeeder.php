<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Urutan seeder penting — jangan diubah karena ada relasi FK.
     */
    public function run(): void
    {
        $this->command->info('🌱 Memulai seeding database PawTrainer...');
        $this->command->newLine();

        $this->call([
            AdminSeeder::class,    // 1. Buat user admin
            UserSeeder::class,     // 2. Buat user demo
            CategorySeeder::class, // 3. Buat kategori awal
        ]);

        $this->command->newLine();
        $this->command->info('🎉 Seeding selesai!');
    }
}
