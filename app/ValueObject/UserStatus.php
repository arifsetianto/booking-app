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
}
