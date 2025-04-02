<?php

namespace App\Http\Responses\Auth;

use Illuminate\Http\JsonResponse;

class AuthResponse
{
    public static function success(array $data, string $message = null, int $statusCode = 200): JsonResponse
    {
        $response = [
            'success' => true,
        ];

        if ($message) {
            $response['message'] = $message;
        }

        if (!empty($data)) {
            $response = array_merge($response, $data);
        }

        return response()->json($response, $statusCode);
    }

    public static function error(string $message, int $statusCode = 400): JsonResponse
    {
        return response()->json([
            'success' => false,
            'error' => $message
        ], $statusCode);
    }
}
