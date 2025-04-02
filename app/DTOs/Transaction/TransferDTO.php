<?php

namespace App\DTOs\Transaction;

class TransferDTO
{
    public function __construct(
        public readonly int     $senderId,
        public readonly int     $receiverId,
        public readonly float   $amount,
        public readonly ?string $description = null
    )
    {
    }
}
