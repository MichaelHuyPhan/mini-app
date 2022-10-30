<?php

namespace App\Http\Controllers;

use App\Http\Requests\LoanCreateRequest;
use App\Http\Requests\LoanRepayRequest;
use App\Models\Loan;
use App\Models\ScheduledRepayment;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

class LoanController extends Controller
{
    public const WEEKLY_REPAYMENT = 7;

    public function create(LoanCreateRequest $request)
    {
        $loan = Loan::createNewLoan(
            $request->user(),
            $request->amount,
            $request->term,
            self::WEEKLY_REPAYMENT
        );

        return response([
            'loan' => $loan,
            'scheduled_repayment' => $loan->scheduledRepayments()->get()
        ]);
    }

    public function get(Request $request)
    {
        $user = Auth::user();
        $loans = $user->loans()->with('scheduledRepayments')->get();

        return response([
            'loans' => $loans,
        ]);
    }

    public function repay(LoanRepayRequest $request)
    {
        $loan_id = $request->loan_id;
        $amount = $request->amount;
        $user = Auth::user();
        $loan = $user->loans()->where('id', $loan_id)->first();

        if (!$loan || $loan->status != Loan::STATUS_APPROVED) {
            return response([
                'message' => 'The selected loan is invalid'
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        $latestPendingRepayment = ScheduledRepayment::getLatestPendingRepayment($loan);

        if (! $latestPendingRepayment) {
            return response([
                'message' => 'You already paid',
            ]);
        }
        if ($amount < $latestPendingRepayment->amount) {
            return response([
                'message' => 'Not enough money'
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        $latestPendingRepayment->payScheduledRepayment();

        return response([
            'loan' => $loan,
            'latest_repayment' => $latestPendingRepayment
        ]);
    }
}
