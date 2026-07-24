<?php

namespace Elegantly\Impersonator;

use Elegantly\Impersonator\Events\LeaveImpersonation;
use Elegantly\Impersonator\Events\TakeImpersonation;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Session;

class Impersonator
{
    public function isImpersonating(): bool
    {
        return Session::has('impersonator');
    }

    public function getImpersonatorId(): null|int|string
    {
        /** @var null|int|string */
        return Session::get('impersonator');
    }

    public function getImpersonator(): ?Authenticatable
    {
        if ($impersonatorId = $this->getImpersonatorId()) {
            return Auth::getProvider()->retrieveById($impersonatorId);
        }

        return null;
    }

    public function take(?Authenticatable $user): bool
    {

        if ($user === null) {
            return $this->leave();
        }

        $impersonator = $this->getImpersonator() ?? Auth::user();

        if ($impersonator === null) {
            return false;
        }

        Session::put('impersonator', $impersonator->getAuthIdentifier());

        Auth::login($user);

        Session::regenerate();

        Event::dispatch(new TakeImpersonation($impersonator, $user));

        return true;
    }

    public function leave(): bool
    {
        $impersonator = $this->getImpersonator();

        if ($impersonator === null) {
            return false;
        }

        $impersonated = Auth::user();

        if ($impersonated === null) {
            return false;
        }

        Session::forget('impersonator');

        Auth::login($impersonator);

        Session::regenerate();

        Event::dispatch(new LeaveImpersonation($impersonator, $impersonated));

        return true;

    }
}
