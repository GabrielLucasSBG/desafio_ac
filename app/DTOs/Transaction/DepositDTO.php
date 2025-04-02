<?php

namespace App\DTOs\Transaction;

class DepositDTO
{
    public function __construct(
        public readonly int     $userId,
        public readonly float   $amount,
        public readonly ?string $description = null
    )
    {
    }
}
