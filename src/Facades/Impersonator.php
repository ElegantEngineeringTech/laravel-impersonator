<?php

namespace Elegantly\Impersonator\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @see \Elegantly\Impersonator\Impersonator
 */
class Impersonator extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return \Elegantly\Impersonator\Impersonator::class;
    }
}
