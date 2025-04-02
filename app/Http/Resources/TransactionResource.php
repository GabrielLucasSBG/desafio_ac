<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class TransactionResource extends JsonResource
{
    public function toArray($request)
    {
        $userId = auth()->id();
        $isOutgoing = $this->sender_id === $userId;

        return [
            'id' => $this->id,
            'reference_id' => $this->reference_id,
            'type' => $this->type,
            'direction' => $isOutgoing ? 'outgoing' : 'incoming',
            'amount' => $this->amount,
            'status' => $this->status,
            'description' => $this->description,
            'counterparty' => $isOutgoing
                ? ($this->receiver ? $this->receiver->name : null)
                : ($this->sender ? $this->sender->name : null),
            'created_at' => $this->created_at->format('Y-m-d H:i:s'),
        ];
    }
}
