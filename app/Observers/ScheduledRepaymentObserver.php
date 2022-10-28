<?php

namespace App\Observers;

use App\Models\Loan;
use App\Models\ScheduledRepayment;

class ScheduledRepaymentObserver
{
    public function updated(ScheduledRepayment $scheduledRepayment)
    {
        if ($scheduledRepayment->status == ScheduledRepayment::STATUS_PAID) {
            /* @var Loan $loan */
            $loan = $scheduledRepayment->loan()->first();
            if (! $scheduledRepayment::getLatestPendingRepayment($loan)) {
                $loan->status = Loan::STATUS_PAID;
                $loan->save();
            }
        }
    }
}
