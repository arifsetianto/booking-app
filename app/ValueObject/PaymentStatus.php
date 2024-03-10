<?php

declare(strict_types=1);

namespace App\ValueObject;

/**
 * @author  Arif Setianto <arifsetiantoo@gmail.com>
 */
enum PaymentStatus: string
{
    use EnumBehaviourTrait;

    case PENDING = 'pending';
    case PAID = 'paid';
    case EXPIRED = 'expired';
    case CANCELED = 'canceled';

    public function getColor(): string
    {
        return match ($this) {
            self::PENDING => 'blue',
            self::PAID => 'green',
            self::EXPIRED => 'red',
            self::CANCELED => 'pink',
        };
    }
}
