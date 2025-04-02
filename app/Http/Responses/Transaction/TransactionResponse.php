<?php

namespace App\Http\Responses\Transaction;

use App\Models\Transaction;
use Illuminate\Http\JsonResponse;

class TransactionResponse
{
    public static function success(Transaction $transaction, string $message = 'Transação realizada com sucesso'): JsonResponse
    {
        return response()->json([
            'success' => true,
            'message' => $message,
            'data' => [
                'transaction' => [
                    'id' => $transaction->id,
                    'reference_id' => $transaction->reference_id,
                    'type' => $transaction->type,
                    'amount' => $transaction->amount,
                    'status' => $transaction->status,
                    'description' => $transaction->description,
                    'created_at' => $transaction->created_at,
                ],
            ],
        ]);
    }

    public static function error(string $message = 'Erro na transação', int $status = 400): JsonResponse
    {
        return response()->json([
            'success' => false,
            'message' => $message,
        ], $status);
    }

    public static function validationError(array $errors): JsonResponse
    {
        return response()->json([
            'success' => false,
            'message' => 'Erro de validação',
            'errors' => $errors,
        ], 422);
    }
}
