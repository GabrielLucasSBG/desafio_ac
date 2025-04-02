<?php

namespace App\Interfaces\Services;

use App\DTOs\Auth\LoginDTO;
use App\DTOs\Auth\RegisterDTO;
use Illuminate\Http\JsonResponse;

interface AuthServiceInterface
{
    public function register(RegisterDTO $registerDTO): array;

    public function login(LoginDTO $loginDTO): array;

    public function logout(): bool;

    public function getCurrentUser();
}
