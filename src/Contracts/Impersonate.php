<?php

namespace Elegantly\Impersonator;

use Illuminate\Contracts\Auth\Authenticatable;

interface Impersonate
{
    public function canImpersonate(Authenticatable&Impersonate $user): bool;

    public function canBeImpersonatedBy(Authenticatable&Impersonate $user): bool;
}
