<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Notifications\SendTwoFactorCode;
use App\Providers\RouteServiceProvider;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Foundation\Application;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class TwoFactorController extends Controller
{
    public function index(): View|Application|Factory|\Illuminate\Contracts\Foundation\Application
    {
        return view('pages.auth.twoFactor');
    }

    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'two_factor_code' => ['integer', 'required'],
        ]);

        /** @var User $user */
        $user = auth()->user();

        if ($request->input('two_factor_code') !== $user->two_factor_code) {
            throw ValidationException::withMessages([
                'two_factor_code' => __('The code you entered doesn\'t match our records'),
            ]);
        }

        $user->resetTwoFactorCode();

        return redirect()->to(RouteServiceProvider::DASHBOARD);
    }

    public function resend(): RedirectResponse
    {
        /** @var User $user */
        $user = auth()->user();
        $user->generateTwoFactorCode();
        $user->notify(new SendTwoFactorCode());

        return redirect()->back()->withStatus(__('Code has been sent again'));
    }
}
