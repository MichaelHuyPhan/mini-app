<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class LoanRepayRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'loan_id' => [
                'required',
                'numeric',
                'exists:loans,id',
            ],
            'amount' => [
                'required',
                'numeric'
            ]
        ];
    }
}
