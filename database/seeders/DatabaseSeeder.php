<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Buat akun Admin default
        \App\Models\User::create([
            'name' => 'Administrator',
            'username' => 'admin',
            'password' => \Illuminate\Support\Facades\Hash::make('password'),
            'role' => 'admin',
        ]);

        // Panggil seeder data dummy lainnya
        $this->call(DummyDataSeeder::class);
    }
}
