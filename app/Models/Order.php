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
 * @property OrderItem $orderItem
 * @property User $user
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
 * @property \DateTime $revised_at
 * @property Shipping $shipping
 * @property Payment $payment
 * @property boolean $printed
 * @property ?string $error_message
 * @property ?integer $user_order_sequence
 *
 * @author  Arif Setianto <arifsetiantoo@gmail.com>
 */
class Order extends Model
{
    use HasUuids;

    protected $fillable = [
        'email',
        'name',
        'phone',
        'instagram',
        'reason',
        'comment',
    ];

    protected $casts = [
        'qty'                 => 'integer',
        'amount'              => 'integer',
        'confirmed_at'        => 'datetime',
        'verified_at'         => 'datetime',
        'completed_at'        => 'datetime',
        'rejected_at'         => 'datetime',
        'canceled_at'         => 'datetime',
        'revised_at'          => 'datetime',
        'status'              => OrderStatus::class,
        'printed'             => 'boolean',
        'user_order_sequence' => 'integer',
    ];

    public function orderItem(): HasOne
    {
        return $this->hasOne(OrderItem::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

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
