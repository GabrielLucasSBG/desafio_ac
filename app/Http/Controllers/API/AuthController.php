<?php

namespace App\Http\Controllers\API;

use App\DTOs\Auth\LoginDTO;
use App\DTOs\Auth\RegisterDTO;
use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Http\Requests\Auth\RegisterRequest;
use App\Http\Responses\Auth\AuthResponse;
use App\Interfaces\Services\AuthServiceInterface;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    public function __construct(
        private readonly AuthServiceInterface $authService
    )
    {
    }

    public function register(RegisterRequest $request): JsonResponse
    {
        try {
            $dto = RegisterDTO::fromRequest($request->validated());
            $result = $this->authService->register($dto);

            return AuthResponse::success([
                'access_token' => $result['token'],
                'token_type' => 'Bearer',
                'user' => $result['user']
            ], 'Usuário registrado com sucesso', 201);
        } catch (\Exception $e) {
            return AuthResponse::error($e->getMessage());
        }
    }

    public function login(LoginRequest $request): JsonResponse
    {
        try {
            $dto = LoginDTO::fromRequest($request->validated());
            $result = $this->authService->login($dto);

            return AuthResponse::success([
                'access_token' => $result['token'],
                'token_type' => 'Bearer',
                'user' => $result['user']
            ], 'Login realizado com sucesso');
        } catch (ValidationException $e) {
            return AuthResponse::error($e->getMessage(), 422);
        } catch (\Exception $e) {
            return AuthResponse::error($e->getMessage());
        }
    }

    public function logout(Request $request): JsonResponse
    {
        try {
            $this->authService->logout();
            return AuthResponse::success([], 'Logout realizado com sucesso');
        } catch (\Exception $e) {
            return AuthResponse::error($e->getMessage());
        }
    }

    public function user(Request $request): JsonResponse
    {
        try {
            return AuthResponse::success([
                'user' => $this->authService->getCurrentUser()
            ]);
        } catch (\Exception $e) {
            return AuthResponse::error($e->getMessage());
        }
    }
}
