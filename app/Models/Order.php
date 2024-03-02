<?php

declare(strict_types=1);

namespace App\Models;

use App\ValueObject\OrderStatus;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

/**
 * @property int $qty
 * @property int $amount
 * @property OrderStatus $status
 * @property Batch $batch
 * @property Source $source
 * @property string $code
 * @property string|null $comment
 * @property string $email
 * @property string $name
 * @property string $phone
 * @property string $instagram
 * @property string|null $reason
 * @property \DateTime $confirmed_at
 * @property \DateTime $verified_at
 * @property \DateTime $completed_at
 * @property \DateTime $rejected_at
 * @property \DateTime $canceled_at
 *
 * @author  Arif Setianto <arifsetiantoo@gmail.com>
 */
class Order extends Model
{
    use HasUuids;

    protected $casts = [
        'qty'          => 'integer',
        'amount'       => 'integer',
        'confirmed_at' => 'datetime',
        'verified_at'  => 'datetime',
        'completed_at' => 'datetime',
        'rejected_at'  => 'datetime',
        'canceled_at'  => 'datetime',
        'status'       => OrderStatus::class,
    ];

    public function batch(): BelongsTo
    {
        return $this->belongsTo(Batch::class);
    }

    public function source(): BelongsTo
    {
        return $this->belongsTo(Source::class);
    }

    public function shipping(): HasOne
    {
        return $this->hasOne(Shipping::class);
    }

    public function payment(): HasOne
    {
        return $this->hasOne(Payment::class);
    }
}
