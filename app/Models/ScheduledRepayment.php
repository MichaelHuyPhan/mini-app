<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class ScheduledRepayment extends Model
{
    use HasFactory, SoftDeletes;

    public const STATUS_PENDING = 'pending';
    public const STATUS_PAID = 'paid';

    protected $fillable = [
        'repayment_date',
        'amount'
    ];

    protected $hidden = [
        'loan_id',
    ];

    public function loan(): BelongsTo
    {
        return $this->belongsTo(Loan::class);
    }

    public static function getLatestPendingRepayment(Loan $loan): ?ScheduledRepayment
    {
        return ScheduledRepayment::where('loan_id', $loan->id)
            ->where('status', ScheduledRepayment::STATUS_PENDING)
            ->first();
    }

    public function payScheduledRepayment()
    {
        $this->status = self::STATUS_PAID;
        $this->save();
    }
}
