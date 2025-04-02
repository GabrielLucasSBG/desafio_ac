<?php

namespace App\Http\Requests\Transaction;

use Illuminate\Foundation\Http\FormRequest;

class TransferRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'receiver_id' => 'required|integer|exists:users,id',
            'amount' => 'required|numeric|min:0.01',
            'description' => 'nullable|string|max:255',
        ];
    }
}
