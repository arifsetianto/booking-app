<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use App\Models\User;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\Request;

/**
 * @author  Arif Setianto <arifsetiantoo@gmail.com>
 */
class HasRole
{
    public function handle(Request $request, \Closure $next, ...$roles)
    {
        /** @var User $user */
        if (null !== $user = $request->user()) {
            if ($user->hasAnyRoles($roles)) {
                return $next($request);
            }
        }

        throw new AuthorizationException('You are not authorized to perform this action.');
    }
}
