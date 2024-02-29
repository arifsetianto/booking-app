<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
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

        /**
         * @var User $user
         */
        $user = User::query()->where('email', $email)->firstOrFail();

        Auth::login($user);

        return new RedirectResponse(
            url: route('order.list'),
        );
    }
}
