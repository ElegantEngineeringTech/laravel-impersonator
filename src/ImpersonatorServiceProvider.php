<?php

namespace Elegantly\Impersonator;

use Elegantly\Impersonator\Facades\Impersonator;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Support\Facades\Gate;
use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;

class ImpersonatorServiceProvider extends PackageServiceProvider
{
    public function configurePackage(Package $package): void
    {
        /*
         * This class is a Package Service Provider
         *
         * More info: https://github.com/spatie/laravel-package-tools
         */
        $package
            ->name('laravel-impersonator')
            ->hasConfigFile();
    }

    public function packageBooted(): void
    {
        Gate::define('impersonate', static function (?Authenticatable $auth, ?Authenticatable $user) {

            if ($auth === null || $user === null) {
                return false;
            }

            $impersonatorId = Impersonator::getImpersonatorId();
            $authId = $auth->getAuthIdentifier();

            $impersonator = $impersonatorId === $authId ? $auth : Impersonator::getImpersonator();

            if (
                ! $impersonator instanceof Impersonate ||
                ! $impersonator->canImpersonate()
            ) {
                return false;
            }

            if (
                ! $user instanceof Impersonate ||
                ! $user->canBeImpersonated()
            ) {
                return false;
            }

            return true;

        });
    }
}
