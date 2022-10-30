<?php

namespace App\Http\Controllers;

use App\Http\Requests\LoanCreateRequest;
use App\Http\Requests\LoanRepayRequest;
use App\Models\Loan;
use App\Models\ScheduledRepayment;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

class LoanController extends Controller
{
    public const WEEKLY_REPAYMENT = 7;

    public function create(LoanCreateRequest $request): JsonResponse
    {
        $loan = Loan::createNewLoan(
            $request->user(),
            $request->amount,
            $request->term,
            self::WEEKLY_REPAYMENT
        );

        return new JsonResponse([
            'loan' => $loan,
            'scheduled_repayment' => $loan->scheduledRepayments()->get()
        ]);
    }

    public function get(Request $request): JsonResponse
    {
        $user = Auth::user();
        $loans = $user->loans()->with('scheduledRepayments')->get();

        return new JsonResponse([
            'loans' => $loans,
        ]);
    }

    public function repay(LoanRepayRequest $request): JsonResponse
    {
        $loan_id = $request->loan_id;
        $amount = $request->amount;
        $user = Auth::user();
        $loan = $user->loans()->where('id', $loan_id)->first();

        if (!$loan || $loan->status != Loan::STATUS_APPROVED) {
            return new JsonResponse([
                'message' => 'The selected loan is invalid'
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        $latestPendingRepayment = ScheduledRepayment::getLatestPendingRepayment($loan);

        if (! $latestPendingRepayment) {
            return new JsonResponse([
                'message' => 'You already paid',
            ]);
        }
        if ($amount < $latestPendingRepayment->amount) {
            return new JsonResponse([
                'message' => 'Not enough money'
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        $latestPendingRepayment->payScheduledRepayment();

        return new JsonResponse([
            'loan' => $loan,
            'latest_repayment' => $latestPendingRepayment
        ]);
    }
}
