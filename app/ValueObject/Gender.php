<?php

declare(strict_types=1);

namespace App\ValueObject;

/**
 * @author  Arif Setianto <arifsetiantoo@gmail.com>
 */
enum Gender: string
{
    use EnumBehaviourTrait;

    case MALE = 'male';
    case FEMALE = 'female';
}
