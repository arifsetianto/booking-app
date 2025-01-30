<?php

declare(strict_types=1);

namespace App\Event\Auth;

use App\Models\User;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Queue\SerializesModels;

/**
 * @author  Arif Setianto <arifsetiantoo@gmail.com>
 */
class NewMemberRegistered
{
    use SerializesModels;

    /**
     * The authenticated user.
     *
     * @var Authenticatable|User
     */
    public Authenticatable|User $user;

    /**
     * Create a new event instance.
     *
     * @param Authenticatable|User $user
     * @return void
     */
    public function __construct(User|Authenticatable $user)
    {
        $this->user = $user;
    }
}
