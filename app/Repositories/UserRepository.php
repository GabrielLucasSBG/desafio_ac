<?php

namespace App\Repositories;

use App\Interfaces\Repositories\UserRepositoryInterface;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserRepository implements UserRepositoryInterface
{
    public function create(array $userData): User
    {
        return User::create([
            'name' => $userData['name'],
            'email' => $userData['email'],
            'password' => Hash::make($userData['password']),
        ]);
    }

    public function findByEmail(string $email): ?User
    {
        return User::where('email', $email)->first();
    }
}
