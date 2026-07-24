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
    public function __construct(
        public string $session_key
    ) {
        //
    }

    protected ?Authenticatable $impersonator = null;

    public function isImpersonating(): bool
    {
        return Session::has($this->sessionKey());
    }

    public function isNotImpersonating(): bool
    {
        return ! $this->isImpersonating();
    }

    public function getImpersonatorId(): null|int|string
    {
        /** @var null|int|string */
        return Session::get($this->sessionKey());
    }

    public function getImpersonator(): ?Authenticatable
    {
        $impersonatorId = $this->getImpersonatorId();

        if ($impersonatorId === null) {
            return null;
        }

        if (
            $this->impersonator &&
            $this->impersonator->getAuthIdentifier() === $impersonatorId
        ) {
            return $this->impersonator;
        }

        return $this->impersonator = Auth::getProvider()->retrieveById($impersonatorId);

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

        Session::put($this->sessionKey(), $impersonator->getAuthIdentifier());

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

        /** @var ?Authenticatable */
        $impersonated = Auth::user();

        Session::forget($this->sessionKey());

        Auth::login($impersonator);

        Session::regenerate();

        $this->impersonator = null;

        Event::dispatch(new LeaveImpersonation($impersonator, $impersonated));

        return true;

    }

    private function sessionKey(): string
    {
        return $this->session_key;
    }
}
