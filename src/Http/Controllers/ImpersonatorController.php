<?php

namespace Elegantly\Impersonator\Http\Controllers;

use Elegantly\Impersonator\Facades\Impersonator;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;

class ImpersonatorController extends Controller
{
    public function take(string|int $user): RedirectResponse
    {
        $impersonated = Auth::getProvider()->retrieveById($user);

        Gate::authorize('impersonate', $impersonated);

        Impersonator::take($impersonated);

        return redirect()->back();
    }

    public function leave(): RedirectResponse
    {
        Impersonator::leave();

        return redirect()->back();
    }
}
