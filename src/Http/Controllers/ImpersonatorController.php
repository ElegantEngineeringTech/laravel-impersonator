<?php

namespace Elegantly\Impersonator\Http\Controllers;

use Elegantly\Impersonator\Facades\Impersonator;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;

class ImpersonatorController extends Controller
{
    public function take(Authenticatable|string|int $user): RedirectResponse
    {
        $impersonated = $user instanceof Authenticatable ? $user : Auth::getProvider()->retrieveById($user);

        Gate::authorize('impersonate', $impersonated);

        Impersonator::take($impersonated);

        return redirect()->intended(
            url()->previous('/')
        );
    }

    public function leave(): RedirectResponse
    {
        Impersonator::leave();

        return redirect()->intended(
            url()->previous('/')
        );
    }
}
