<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    public function run(): void
    {
        User::updateOrCreate(
            ['email' => 'admin@pawtrainer.com'],
            [
                'name'     => 'Super Admin',
                'email'    => 'admin@pawtrainer.com',
                'password' => Hash::make('password'),
                'role'     => 'admin',
            ]
        );

        $this->command->info('✅ Admin seeder selesai: admin@pawtrainer.com');
    }
}
