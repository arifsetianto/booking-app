<?php

declare(strict_types=1);

namespace App\Models;

use App\ValueObject\Gender;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int $qty
 * @property int $amount
 * @property Gender $gender
 * @property Order $order
 * @property Religion $religion
 * @property Designation $designation
 * @property string $receiver_en_name
 * @property string $receiver_th_name
 * @property string $identity_file
 *
 * @author  Arif Setianto <arifsetiantoo@gmail.com>
 */
class OrderItem extends Model
{
    use HasUuids;

    protected $casts = [
        'qty'    => 'integer',
        'amount' => 'integer',
        'gender' => Gender::class,
    ];

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public function religion(): BelongsTo
    {
        return $this->belongsTo(Religion::class);
    }

    public function designation(): BelongsTo
    {
        return $this->belongsTo(Designation::class);
    }
}
