<?php

namespace App\Interfaces\Repositories;

use App\Models\User;

interface UserRepositoryInterface
{
    public function create(array $userData): User;

    public function findByEmail(string $email): ?User;
}
