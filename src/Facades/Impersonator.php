<?php

namespace Elegantly\Impersonator\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @method static \Illuminate\Contracts\Auth\Authenticatable|null getImpersonator()
 * @method static int|null getImpersonatorId()
 * @method static bool isImpersonating()
 * @method static void take(null|int|\Illuminate\Contracts\Auth\Authenticatable $user)
 * @method static void leave()
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
