<?php

namespace App\Repositories;

use App\Interfaces\Repositories\TransactionRepositoryInterface;
use App\Models\Transaction;
use Illuminate\Database\Eloquent\Collection;

class TransactionRepository implements TransactionRepositoryInterface
{
    /**
     * Cria uma nova transação.
     *
     * @param array $data
     * @return Transaction
     */
    public function create(array $data): Transaction
    {
        return Transaction::create($data);
    }

    /**
     * Encontra uma transação pelo ID.
     *
     * @param int $id
     * @return Transaction|null
     */
    public function findById(int $id): ?Transaction
    {
        return Transaction::find($id);
    }

    /**
     * Atualiza os dados de uma transação.
     *
     * @param Transaction $transaction
     * @param array $data
     * @return bool
     */
    public function update(Transaction $transaction, array $data): bool
    {
        return $transaction->update($data);
    }

    /**
     * Obtém transações de um usuário.
     *
     * @param int $userId
     * @return Collection
     */
    public function getUserTransactions(int $userId): Collection
    {
        return Transaction::where('sender_id', $userId)
            ->orWhere('receiver_id', $userId)
            ->orderBy('created_at', 'desc')
            ->get();
    }
}
