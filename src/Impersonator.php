<?php

namespace Elegantly\Impersonator;

use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

class Impersonator
{
    public function getImpersonator(): ?Authenticatable
    {
        if ($impersonatorId = $this->getImpersonatorId()) {
            return Auth::getProvider()->retrieveById($impersonatorId);
        }

        return null;
    }

    public function getImpersonatorId(): ?int
    {
        /** @var ?int */
        return Session::get('impersonator');
    }

    public function isImpersonating(): bool
    {
        return Session::has('impersonator');
    }

    public function take(null|int|Authenticatable $user): void
    {
        if ($user === null) {
            $this->leave();

            return;
        }

        Session::put('impersonator', Auth::id());

        if ($user instanceof Authenticatable) {
            Auth::login($user);
        } else {
            Auth::loginUsingId($user);
        }

        Session::regenerate();
    }

    public function leave(): void
    {

        if ($impersonatorId = Session::pull('impersonator')) {
            Auth::loginUsingId($impersonatorId);

            Session::regenerate();
        }

    }
}
