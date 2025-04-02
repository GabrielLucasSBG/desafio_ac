<?php

namespace App\Interfaces\Repositories;

use App\Models\Transaction;
use Illuminate\Database\Eloquent\Collection;

interface TransactionRepositoryInterface
{
    /**
     * Cria uma nova transação.
     *
     * @param array $data
     * @return Transaction
     */
    public function create(array $data): Transaction;

    /**
     * Encontra uma transação pelo ID.
     *
     * @param int $id
     * @return Transaction|null
     */
    public function findById(int $id): ?Transaction;

    /**
     * Atualiza os dados de uma transação.
     *
     * @param Transaction $transaction
     * @param array $data
     * @return bool
     */
    public function update(Transaction $transaction, array $data): bool;

    /**
     * Obtém transações de um usuário.
     *
     * @param int $userId
     * @return Collection
     */
    public function getUserTransactions(int $userId): Collection;
}
