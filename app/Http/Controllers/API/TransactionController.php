<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Resources\TransactionResource;
use App\Interfaces\Services\TransactionServiceInterface;
use App\DTOs\Transaction\TransferDTO;
use App\DTOs\Transaction\DepositDTO;
use App\DTOs\Transaction\ReversalDTO;
use App\Http\Requests\Transaction\TransferRequest;
use App\Http\Requests\Transaction\DepositRequest;
use App\Http\Requests\Transaction\ReversalRequest;
use App\Http\Responses\Transaction\TransactionResponse;
use Illuminate\Http\JsonResponse;

class TransactionController extends Controller
{
    protected TransactionServiceInterface $transactionService;

    public function __construct(TransactionServiceInterface $transactionService)
    {
        $this->transactionService = $transactionService;
    }

    /**
     * Realiza a transferência de um valor do usuário autenticado para outro usuário
     */
    public function transfer(TransferRequest $request): JsonResponse
    {
        $senderId = auth()->id();

        $transferDTO = new TransferDTO(
            $senderId,
            $request->receiver_id,
            $request->amount,
            $request->description
        );

        $result = $this->transactionService->transfer($transferDTO);

        if (!$result['success']) {
            return TransactionResponse::error($result['message']);
        }

        return TransactionResponse::success($result['transaction'], $result['message']);
    }

    /**
     * Realizar depósito para o usuário autenticado
     */
    public function deposit(DepositRequest $request): JsonResponse
    {
        $userId = auth()->id();

        $depositDTO = new DepositDTO(
            $userId,
            $request->amount,
            $request->description
        );

        $result = $this->transactionService->deposit($depositDTO);

        if (!$result['success']) {
            return TransactionResponse::error($result['message']);
        }

        return TransactionResponse::success($result['transaction'], $result['message']);
    }

    /**
     * Reverter uma transação com os dados fornecidos na solicitação.
     */
    public function reverse(ReversalRequest $request): JsonResponse
    {
        $reversalDTO = new ReversalDTO(
            $request->transaction_id,
            $request->description
        );

        $result = $this->transactionService->reverse($reversalDTO);

        if (!$result['success']) {
            return TransactionResponse::error($result['message']);
        }

        return TransactionResponse::success($result['transaction'], $result['message']);
    }

    /**
     * Obter o histórico de transações do usuário atual
     */
    public function history(): JsonResponse
    {
        $userId = auth()->id();
        $transactions = $this->transactionService->getUserTransactions($userId);

        return response()->json([
            'success' => true,
            'data' => [
                'transactions' => TransactionResource::collection($transactions)
            ]
        ]);
    }
}
