<?php

declare(strict_types=1);

namespace App\ValueObject;

/**
 * @author  Arif Setianto <arifsetiantoo@gmail.com>
 */
enum BatchStatus: string
{
    use EnumBehaviourTrait;

    case PUBLISHED = 'published';
    case COMPLETED = 'completed';
    case CLOSED = 'closed';

    public function getColor(): string
    {
        return match ($this) {
            self::PUBLISHED => 'blue',
            self::COMPLETED => 'green',
            self::CLOSED => 'red',
        };
    }
}
