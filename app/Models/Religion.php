<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

/**
 * @author  Arif Setianto <arifsetiantoo@gmail.com>
 */
class Religion extends Model
{
    use HasUuids;

    public $timestamps = false;
}
