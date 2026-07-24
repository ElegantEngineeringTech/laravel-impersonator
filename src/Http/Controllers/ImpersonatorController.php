<?php

declare(strict_types=1);

namespace Elegantly\Impersonator\Http\Controllers;

use Elegantly\Impersonator\Facades\Impersonator;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;

class ImpersonatorController extends Controller
{
    protected function redirect(?Authenticatable $impersonated): string|RedirectResponse
    {
        return redirect()->intended(
            url()->previous('/')
        );
    }

    protected function impersonate(Authenticatable|string|int $user): ?Authenticatable
    {
        $impersonated = $user instanceof Authenticatable ? $user : Auth::getProvider()->retrieveById($user);

        Gate::authorize('impersonate', $impersonated);

        Impersonator::take($impersonated);

        return $impersonated;
    }

    public function take(Authenticatable|string|int $user): string|RedirectResponse
    {
        return $this->redirect(
            $this->impersonate($user)
        );
    }

    public function leave(): string|RedirectResponse
    {
        Impersonator::leave();

        return $this->redirect(null);
    }
}
