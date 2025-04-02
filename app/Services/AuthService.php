<?php

namespace App\Services;

use App\DTOs\Auth\LoginDTO;
use App\DTOs\Auth\RegisterDTO;
use App\Interfaces\Repositories\UserRepositoryInterface;
use App\Interfaces\Services\AuthServiceInterface;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class AuthService implements AuthServiceInterface
{
    public function __construct(
        private readonly UserRepositoryInterface $userRepository
    )
    {
    }

    public function register(RegisterDTO $registerDTO): array
    {
        $user = $this->userRepository->create([
            'name' => $registerDTO->name,
            'email' => $registerDTO->email,
            'password' => $registerDTO->password,
        ]);

        $token = $user->createToken('auth_token')->plainTextToken;

        return [
            'user' => $user,
            'token' => $token
        ];
    }

    public function login(LoginDTO $loginDTO): array
    {
        if (!Auth::attempt(['email' => $loginDTO->email, 'password' => $loginDTO->password])) {
            throw ValidationException::withMessages([
                'email' => ['As credenciais fornecidas estão incorretas.'],
            ]);
        }

        $user = $this->userRepository->findByEmail($loginDTO->email);
        $token = $user->createToken('auth_token')->plainTextToken;

        return [
            'user' => $user,
            'token' => $token
        ];
    }

    public function logout(): bool
    {
        if (Auth::check()) {
            Auth::user()->currentAccessToken()->delete();
            return true;
        }

        return false;
    }

    public function getCurrentUser()
    {
        return Auth::user();
    }
}
