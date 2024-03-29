<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use App\Models\User;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

/**
 * @author  Arif Setianto <arifsetiantoo@gmail.com>
 */
class ForceAuthenticate
{
    public function handle(Request $request, \Closure $next)
    {
        /** @var User $user */
        if (null !== $user = User::where('email', $request->query->get('email'))->first()) {
            Auth::login($user);

            return $next($request);
        }

        throw new AuthorizationException('You are not authorized to perform this action.');
    }
}
