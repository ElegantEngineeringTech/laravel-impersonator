<?php

namespace Elegantly\Impersonator\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @method static \Illuminate\Contracts\Auth\Authenticatable|null getImpersonator()
 * @method static int|string|null getImpersonatorId()
 * @method static bool isImpersonating()
 * @method static bool take(?\Illuminate\Contracts\Auth\Authenticatable $user)
 * @method static bool leave()
 *
 * @see \Elegantly\Impersonator\Impersonator
 */
class Impersonator extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return \Elegantly\Impersonator\Impersonator::class;
    }
}
