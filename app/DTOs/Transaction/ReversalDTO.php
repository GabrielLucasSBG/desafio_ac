<?php

namespace App\DTOs\Transaction;

class ReversalDTO
{
    public function __construct(
        public readonly int  $transactionId,
        public readonly ?string $description = null
    )
    {
    }
}
