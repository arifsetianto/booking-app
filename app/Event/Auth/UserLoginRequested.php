<?php

declare(strict_types=1);

namespace App\Event\Auth;

/**
 * @author  Arif Setianto <arifsetiantoo@gmail.com>
 */
class UserLoginRequested
{
    public function __construct(public string $email)
    {
    }

    public function getEmail(): string
    {
        return $this->email;
    }
}
