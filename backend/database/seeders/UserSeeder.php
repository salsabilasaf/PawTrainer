<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        User::updateOrCreate(
            ['email' => 'user@pawtrainer.com'],
            [
                'name'     => 'User Demo',
                'email'    => 'user@pawtrainer.com',
                'password' => Hash::make('password'),
                'role'     => 'user',
            ]
        );

        $this->command->info('✅ User seeder selesai: user@pawtrainer.com');
    }
}
