<?php

declare(strict_types=1);

namespace App\Models;

use App\ValueObject\BatchStatus;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

/**
 * @property integer $number
 * @property integer $total_stock
 * @property integer $purchased_stock
 * @property BatchStatus $status
 *
 * @author  Arif Setianto <arifsetiantoo@gmail.com>
 */
class Batch extends Model
{
    use HasUuids;

    protected $casts = [
        'status' => BatchStatus::class,
        'available_stock' => 'integer',
        'purchased_stock' => 'integer',
    ];

    public static function create(int $availableStock): static
    {
        $batch = new Batch();
        $batch->number = Batch::count() + 1;
        $batch->total_stock = $availableStock;
        $batch->status = BatchStatus::PUBLISHED;

        $batch->save();

        return $batch;
    }
}
