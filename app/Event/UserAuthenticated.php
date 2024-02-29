<?php

declare(strict_types=1);

namespace App\Event;

use App\Models\User;
use Illuminate\Queue\SerializesModels;

/**
 * @author  Arif Setianto <arifsetiantoo@gmail.com>
 */
class UserAuthenticated
{
    use SerializesModels;

    public function __construct(protected User $user)
    {
    }

    public function getUser(): User
    {
        return $this->user;
    }
}
