<?php

namespace Database\Seeders;

use App\Models\User;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        User::create([
            'name' => 'Primeiro',
            'email' => 'primeiro@example.com',
            'password' => Hash::make('password'),
            'balance' => 200
        ]);

        User::create([
            'name' => 'Usuário Comum',
            'email' => 'user@example.com',
            'password' => Hash::make('password'),
            'balance' => 100
        ]);
    }
}
