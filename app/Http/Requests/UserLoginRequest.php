<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UserLoginRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'email' => [
                'required',
                'email',
                'exists:users,email'
            ],
            'password' => [
                'required'
            ]
        ];
    }

    public function messages()
    {
        return [
            'email.exists' => 'The provided credentials are incorrect',
        ];
    }
}
