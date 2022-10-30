<?php

namespace App\Http\Controllers;

use App\Http\Requests\UserLoginRequest;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class UserController extends Controller
{
    public function login(UserLoginRequest $request): JsonResponse
    {
        $user = User::where('email', $request->email)->first();

        if (! Hash::check($request->password, $user->password)) {
            throw ValidationException::withMessages([
                'password' => ['The provided credentials are incorrect']
            ]);
        }

        return new JsonResponse([
            'token' => $user->createToken($request->email)->plainTextToken,
            'token_type' => 'Bearer',
        ]);
    }
}
