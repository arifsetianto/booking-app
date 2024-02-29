<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Profile;
use App\Models\Role;
use App\Models\User;
use App\ValueObject\UserStatus;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response as ResponseAlias;

class LoginEmailController extends Controller
{
    public function __invoke(Request $request, string $email): RedirectResponse
    {
        if (! $request->hasValidSignature()) {
            abort(ResponseAlias::HTTP_UNAUTHORIZED);
        }

        if (null === $user = User::query()->where('email', $email)->first()) {
            /** @var User $user */
            event(new Registered($user = User::create(['email' => $email, 'status' => UserStatus::NEW])));
            $user->roles()->attach(Role::where('name', 'customer')->first());
            $user->markEmailAsVerified();
            $user->profile()->associate(Profile::create());

            $user->save();
        }

        Auth::login($user);

        if ($user->status->is(UserStatus::NEW)) {
            return new RedirectResponse(
                url: route('profile.complete'),
            );
        }

        return new RedirectResponse(
            url: route('order.list'),
        );
    }
}
