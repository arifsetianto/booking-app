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
    case REVISED = 'revised';

    public function getColor(): string
    {
        return match ($this) {
            self::DRAFT => 'gray',
            self::PENDING => 'amber',
            self::CONFIRMED => 'yellow',
            self::VERIFIED => 'blue',
            self::COMPLETED => 'green',
            self::REJECTED => 'red',
            self::CANCELED => 'pink',
            self::REVISED => 'fuchsia',
        };
    }
}
