<?php

declare(strict_types=1);

namespace App\ValueObject;

/**
 * @author  Arif Setianto <arifsetiantoo@gmail.com>
 */
enum UserStatus: string
{
    use EnumBehaviourTrait;

    case NEW = 'new';
    case COMPLETED = 'completed';

    public function getColor(): string
    {
        return match ($this) {
            self::NEW => 'red',
            self::COMPLETED => 'green',
        };
    }

    public function getLabel(): string
    {
        return match ($this) {
            self::NEW => 'not completed',
            self::COMPLETED => 'completed',
        };
    }
}
