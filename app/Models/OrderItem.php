<?php

declare(strict_types=1);

namespace App\Models;

use App\ValueObject\Gender;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int $qty
 * @property float $amount
 * @property Gender $gender
 * @property Order $order
 * @property Religion $religion
 * @property Designation $designation
 * @property string $receiver_en_name
 * @property string $receiver_th_name
 * @property string $identity_file
 * @property string $identity_file_hash
 *
 * @author  Arif Setianto <arifsetiantoo@gmail.com>
 */
class OrderItem extends Model
{
    use HasUuids;

    protected $fillable = [
        'receiver_en_name',
        'receiver_th_name',
        'qty',
        'amount',
        'gender'
    ];

    protected $casts = [
        'qty'    => 'integer',
        'amount' => 'float',
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
