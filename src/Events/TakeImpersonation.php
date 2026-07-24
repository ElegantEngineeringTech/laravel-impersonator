<?php

namespace Elegantly\Impersonator\Events;

use Illuminate\Contracts\Auth\Authenticatable;

final readonly class TakeImpersonation
{
    public function __construct(
        public Authenticatable $impersonator,
        public Authenticatable $impersonated,
    ) {}
}
