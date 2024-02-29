<?php

declare(strict_types=1);

namespace App\Models;

use App\ValueObject\Gender;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property Gender $gender
 *
 * @author  Arif Setianto <arifsetiantoo@gmail.com>
 */
class Profile extends Model
{
    use HasUuids;

    protected $fillable = [
        'phone',
        'instagram',
        'gender',
        'religion_id',
        'address',
    ];

    protected $casts = [
        'gender' => Gender::class,
    ];

    /**
     * @return BelongsTo
     */
    public function religion(): BelongsTo
    {
        return $this->belongsTo(Religion::class);
    }
}
