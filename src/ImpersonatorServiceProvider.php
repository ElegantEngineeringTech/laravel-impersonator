<?php

namespace Elegantly\Impersonator;

use Elegantly\Impersonator\Contracts\Impersonate;
use Elegantly\Impersonator\Facades\Impersonator;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Support\Facades\Blade;
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
        Blade::if('impersonating', function (mixed $condition = true) {
            return Impersonator::isImpersonating() && value($condition);
        });

        Gate::define('impersonate', static function (?Authenticatable $auth, ?Authenticatable $user) {

            if ($auth === null || $user === null) {
                return false;
            }

            if ($auth->getAuthIdentifier() === $user->getAuthIdentifier()) {
                return false;
            }

            $impersonator = Impersonator::getImpersonator() ?? $auth;

            if (
                ! $impersonator instanceof Impersonate ||
                ! $user instanceof Impersonate
            ) {
                return false;
            }

            if (! $impersonator->canImpersonate($user)) {
                return false;
            }

            if (! $user->canBeImpersonatedBy($impersonator)) {
                return false;
            }

            return true;

        });
    }
}
