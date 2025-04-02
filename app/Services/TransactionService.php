<?php

namespace App\Services;

use App\Interfaces\Services\TransactionServiceInterface;
use App\DTOs\Transaction\TransferDTO;
use App\DTOs\Transaction\DepositDTO;
use App\DTOs\Transaction\ReversalDTO;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class TransactionService implements TransactionServiceInterface
{
    /**
     * Transferir dinheiro entre usuários
     */
    public function transfer(TransferDTO $transferDTO): array
    {
        try {
            return DB::transaction(function () use ($transferDTO) {
                $sender = User::findOrFail($transferDTO->senderId);
                $receiver = User::findOrFail($transferDTO->receiverId);

                if ($sender->balance < $transferDTO->amount) {
                    return [
                        'success' => false,
                        'message' => 'Saldo insuficiente para realizar a transferência',
                    ];
                }

                $sender->balance -= $transferDTO->amount;
                $sender->save();

                $receiver->balance += $transferDTO->amount;
                $receiver->save();

                $transaction = Transaction::create([
                    'sender_id' => $transferDTO->senderId,
                    'receiver_id' => $transferDTO->receiverId,
                    'amount' => $transferDTO->amount,
                    'type' => 'transfer',
                    'reference_id' => Str::uuid()->toString(),
                    'description' => $transferDTO->description ?? 'Transferência de fundos',
                ]);

                return [
                    'success' => true,
                    'message' => 'Transferência realizada com sucesso',
                    'transaction' => $transaction,
                ];
            });
        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => 'Erro ao processar a transferência: ' . $e->getMessage(),
            ];
        }
    }

    /**
     * Depositar dinheiro na conta do usuário
     */
    public function deposit(DepositDTO $depositDTO): array
    {
        try {
            return DB::transaction(function () use ($depositDTO) {
                $user = User::findOrFail($depositDTO->userId);
                $user->balance += $depositDTO->amount;
                $user->save();

                $transaction = Transaction::create([
                    'receiver_id' => $depositDTO->userId,
                    'amount' => $depositDTO->amount,
                    'type' => 'deposit',
                    'reference_id' => Str::uuid()->toString(),
                    'description' => $depositDTO->description ?? 'Depósito de fundos',
                ]);

                return [
                    'success' => true,
                    'message' => 'Depósito realizado com sucesso',
                    'transaction' => $transaction,
                ];
            });
        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => 'Erro ao processar o depósito: ' . $e->getMessage(),
            ];
        }
    }

    /**
     * Reverter uma transação
     */
    public function reverse(ReversalDTO $reversalDTO): array
    {
        try {
            return DB::transaction(function () use ($reversalDTO) {
                $transaction = Transaction::where('id', $reversalDTO->transactionId)
                    ->where('status', 'completed')
                    ->firstOrFail();

                if ($transaction->type === 'reversal') {
                    return [
                        'success' => false,
                        'message' => 'Não é possível reverter uma reversão',
                    ];
                }

                // Atualizar status da transação original
                $transaction->status = 'reversed';
                $transaction->save();

                // Processar reversão de acordo com o tipo de transação
                if ($transaction->type === 'transfer') {
                    $sender = User::find($transaction->sender_id);
                    $receiver = User::find($transaction->receiver_id);

                    if ($receiver->balance < $transaction->amount) {
                        return [
                            'success' => false,
                            'message' => 'O destinatário não possui saldo suficiente para reverter a transferência'
                        ];
                    }

                    $sender->balance += $transaction->amount;
                    $sender->save();

                    $receiver->balance -= $transaction->amount;
                    $receiver->save();

                    $reversalTransaction = Transaction::create([
                        'sender_id' => $transaction->receiver_id,
                        'receiver_id' => $transaction->sender_id,
                        'amount' => $transaction->amount,
                        'type' => 'reversal',
                        'reference_id' => Str::uuid()->toString(),
                        'description' => $reversalDTO->description ?? 'Reversão da transação: ' . $transaction->reference_id,
                    ]);

                } elseif ($transaction->type === 'deposit') {
                    $user = User::find($transaction->receiver_id);

                    if ($user->balance < $transaction->amount) {
                        return [
                            'success' => false,
                            'message' => 'Usuário não possui saldo suficiente para reverter o depósito'
                        ];
                    }

                    $user->balance -= $transaction->amount;
                    $user->save();

                    $reversalTransaction = Transaction::create([
                        'sender_id' => $transaction->receiver_id,
                        'amount' => $transaction->amount,
                        'type' => 'reversal',
                        'reference_id' => Str::uuid()->toString(),
                        'description' => $reversalDTO->description ?? 'Reversão do depósito: ' . $transaction->reference_id,
                    ]);
                }

                return [
                    'success' => true,
                    'message' => 'Transação revertida com sucesso',
                    'transaction' => $reversalTransaction ?? null,
                ];
            });
        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => 'Erro ao reverter a transação: ' . $e->getMessage(),
            ];
        }
    }

    /**
     * Obter o histórico de transações do usuário
     */
    public function getUserTransactions(int $userId): object
    {
        try {
            return Transaction::where('sender_id', $userId)
                ->orWhere('receiver_id', $userId)
                ->orderBy('created_at', 'desc')
                ->get();

        } catch (\Exception $e) {
            return collect();
        }
    }
}
