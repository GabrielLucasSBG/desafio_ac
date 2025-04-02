<?php

namespace App\Http\Requests\Transaction;

use Illuminate\Foundation\Http\FormRequest;

class ReversalRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'transaction_id' => 'required|integer|exists:transactions,id',
            'description' => 'nullable|string|max:255',
        ];
    }
}
