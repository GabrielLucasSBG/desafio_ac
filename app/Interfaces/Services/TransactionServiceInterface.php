<?php

namespace App\Interfaces\Services;

use App\DTOs\Transaction\TransferDTO;
use App\DTOs\Transaction\DepositDTO;
use App\DTOs\Transaction\ReversalDTO;

interface TransactionServiceInterface
{
    /**
     * Transferir dinheiro entre usuários
     */
    public function transfer(TransferDTO $transferDTO): array;

    /**
     * Depositar dinheiro na conta do usuário
     */
    public function deposit(DepositDTO $depositDTO): array;

    /**
     * Reverter uma transação
     */
    public function reverse(ReversalDTO $reversalDTO): array;

    /**
     * Obter o histórico de transações do usuário
     */
    public function getUserTransactions(int $userId): object;
}
