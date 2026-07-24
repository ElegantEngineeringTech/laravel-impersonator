<?php

namespace Elegantly\Impersonator;

use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Support\Facades\Auth;
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

    public function take(null|int|Authenticatable $user): bool
    {
        if ($user === null) {
            $this->leave();

            return false;
        }

        $impersonatorId = $this->getImpersonatorId() ?? Auth::id();

        Session::put('impersonator', $impersonatorId);

        if ($user instanceof Authenticatable) {
            Auth::login($user);
        } else {
            Auth::loginUsingId($user);
        }

        Session::regenerate();

        return true;
    }

    public function leave(): bool
    {

        if ($impersonatorId = Session::pull('impersonator')) {
            Auth::loginUsingId($impersonatorId);

            Session::regenerate();

            return true;
        }

        return false;

    }
}
