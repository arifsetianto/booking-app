<?php

declare(strict_types=1);

namespace App\Models;

use App\Observers\PaymentObserver;
use App\ValueObject\PaymentStatus;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property Order $order
 * @property \DateTime $expired_at
 * @property \DateTime $paid_at
 * @property \DateTime $canceled_at
 * @property PaymentStatus $status
 * @property string $receipt_file
 *
 * @author  Arif Setianto <arifsetiantoo@gmail.com>
 */
#[ObservedBy([PaymentObserver::class])]
class Payment extends Model
{
    use HasUuids;

    protected $casts = [
        'expired_at'  => 'datetime',
        'paid_at'     => 'datetime',
        'canceled_at' => 'datetime',
        'status'      => PaymentStatus::class,
    ];

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }
}
