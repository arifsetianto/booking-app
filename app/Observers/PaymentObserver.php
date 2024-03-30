<?php

namespace App\Observers;

use App\Event\Payment\PaymentDeleted;
use App\Models\Payment;
use App\ValueObject\PaymentStatus;
use Illuminate\Contracts\Events\ShouldHandleEventsAfterCommit;

class PaymentObserver implements ShouldHandleEventsAfterCommit
{
    /**
     * Handle the Payment "created" event.
     */
    public function created(Payment $payment): void
    {
        //
    }

    /**
     * Handle the Payment "updated" event.
     */
    public function updated(Payment $payment): void
    {
        //
    }

    /**
     * Handle the Payment "deleted" event.
     */
    public function deleted(Payment $payment): void
    {
        if ($payment->status->is(PaymentStatus::PENDING) || $payment->status->is(PaymentStatus::PAID)) {
            event(new PaymentDeleted($payment->order));
        }
    }

    /**
     * Handle the Payment "restored" event.
     */
    public function restored(Payment $payment): void
    {
        //
    }

    /**
     * Handle the Payment "force deleted" event.
     */
    public function forceDeleted(Payment $payment): void
    {
        //
    }
}
