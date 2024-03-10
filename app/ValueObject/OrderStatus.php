<?php

declare(strict_types=1);

namespace App\ValueObject;

/**
 * @author  Arif Setianto <arifsetiantoo@gmail.com>
 */
enum OrderStatus: string
{
    use EnumBehaviourTrait;

    case DRAFT = 'draft';
    case PENDING = 'pending';
    case CONFIRMED = 'confirmed';
    case VERIFIED = 'verified';
    case COMPLETED = 'completed';
    case REJECTED = 'rejected';
    case CANCELED = 'canceled';

    public function getColor(): string
    {
        return match ($this) {
            self::DRAFT => 'black',
            self::PENDING => 'gray',
            self::CONFIRMED => 'yellow',
            self::VERIFIED => 'blue',
            self::COMPLETED => 'green',
            self::REJECTED => 'red',
            self::CANCELED => 'brown',
        };
    }
}
