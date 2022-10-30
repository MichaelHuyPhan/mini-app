<?php

namespace App\Http\Controllers;

use App\Http\Requests\AdminApproveLoanRequest;
use App\Http\Requests\AdminLoginRequest;
use App\Models\Admin;
use App\Models\Loan;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AdminController extends Controller
{
    public function login(AdminLoginRequest $request): JsonResponse
    {
        $admin = Admin::where('email', $request->email)->first();

        if (! Hash::check($request->password, $admin->password)) {
            throw ValidationException::withMessages([
                'password' => ['The provided credentials are incorrect']
            ]);
        }

        return new JsonResponse([
            'token' => $admin->createToken($request->email)->plainTextToken,
            'token_type' => 'Bearer',
        ]);
    }

    public function approveLoan(AdminApproveLoanRequest $request, int $loan_id): JsonResponse
    {
        /* @var Loan $loan */
        $loan = Loan::find($loan_id);
        $loan->approveLoan();

        return new JsonResponse([
            'loan' => $loan
        ]);
    }
}
