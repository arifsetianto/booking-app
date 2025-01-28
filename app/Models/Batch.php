<?php

declare(strict_types=1);

namespace App\Models;

use App\ValueObject\BatchStatus;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

/**
 * @property integer $number
 * @property integer $total_stock
 * @property integer $purchased_stock
 * @property BatchStatus $status
 * @property Carbon|null $publish_at
 *
 * @author  Arif Setianto <arifsetiantoo@gmail.com>
 */
class Batch extends Model
{
    use HasUuids;

    protected $fillable = [
        'number',
        'total_stock',
        'purchased_stock',
        'status',
        'publish_at',
    ];

    protected $casts = [
        'status'          => BatchStatus::class,
        'available_stock' => 'integer',
        'purchased_stock' => 'integer',
        'publish_at'      => 'datetime',
    ];

    public static function create(int $availableStock, ?Carbon $publishAt = null): static
    {
        $batch = new Batch();
        $batch->number = Batch::count() + 1;
        $batch->total_stock = $availableStock;
        $batch->publish_at = $publishAt ?? Carbon::now();

        if (Carbon::now()->lt($batch->publish_at)) {
            $batch->status = BatchStatus::PENDING;
        } else {
            $batch->status = BatchStatus::PUBLISHED;
        }

        $batch->save();

        return $batch;
    }

    public function getAvailableStock(): int
    {
        return $this->total_stock - $this->purchased_stock;
    }
}
