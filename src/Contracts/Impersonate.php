<?php

namespace Elegantly\Impersonator;

interface Impersonate
{
    public function canImpersonate(): bool;

    public function canBeImpersonated(): bool;
}
