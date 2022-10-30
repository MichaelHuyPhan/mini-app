<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Nette\Schema\ValidationException;

class Loan extends Model
{
    use HasFactory;

    public const STATUS_PENDING = 'pending';
    public const STATUS_APPROVED = 'approved';
    public const STATUS_PAID = 'paid';

    protected $fillable = [
        'amount',
        'term',
        'status'
    ];

    protected $hidden = [
        'user_id',
    ];

    public function scheduledRepayments()
    {
        return $this->hasMany(ScheduledRepayment::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public static function createNewLoan(User $customer, float $amount, float $term, int $frequency)
    {
        DB::beginTransaction();

        $loan = $customer->loans()->create([
            'amount' => $amount,
            'term' => $term,
            'status' => self::STATUS_PENDING,
        ]);

        if (! $loan->id) {
            DB::rollBack();
        }

        $scheduledRepaymentAmount = $amount/$term;
        $nextRepayment = Carbon::now()->startOfDay()->addDay($frequency);

        for ($i=0; $i<$term; $i++) {
            $repayment = $loan->scheduledRepayments()->create([
                'repayment_date' => $nextRepayment,
                'amount' => $scheduledRepaymentAmount,
                'status' => self::STATUS_PENDING
            ]);

            if (! $repayment->id) {
                DB::rollBack();
            }

            $nextRepayment->addDay($frequency);
        }

        DB::commit();

        return $loan;
    }

    public function approveLoan(): Loan
    {
        $this->status = self::STATUS_APPROVED;
        $this->save();

        return $this;
    }
}
