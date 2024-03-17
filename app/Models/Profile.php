<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property string $phone
 * @property string $instagram
 * @property Source $source
 *
 * @author  Arif Setianto <arifsetiantoo@gmail.com>
 */
class Profile extends Model
{
    use HasUuids;

    protected $fillable = [
        'phone',
        'instagram',
        'source_id',
    ];

    /**
     * @return BelongsTo
     */
    public function source(): BelongsTo
    {
        return $this->belongsTo(Source::class);
    }
}
