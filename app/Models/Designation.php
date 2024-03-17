<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

/**
 * @property integer $number
 *
 * @author  Arif Setianto <arifsetiantoo@gmail.com>
 */
class Designation extends Model
{
    use HasUuids;

    public $timestamps = false;

    protected $casts = [
        'number' => 'int',
    ];
}
