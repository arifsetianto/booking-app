<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property Order $order
 * @property string $name
 * @property string $phone
 * @property string $address
 * @property int $fee
 * @property SubDistrict $subDistrict
 * @property string $tracking_code
 *
 * @author  Arif Setianto <arifsetiantoo@gmail.com>
 */
class Shipping extends Model
{
    use HasUuids;

    protected $casts = [
        'fee' => 'float',
    ];

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public function subDistrict(): BelongsTo
    {
        return $this->belongsTo(SubDistrict::class);
    }
}
