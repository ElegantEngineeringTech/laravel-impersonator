# User impersonation for Laravel

[![Latest Version on Packagist](https://img.shields.io/packagist/v/elegantly/laravel-impersonator.svg)](https://packagist.org/packages/elegantly/laravel-impersonator)
[![Total Downloads](https://img.shields.io/packagist/dt/elegantly/laravel-impersonator.svg)](https://packagist.org/packages/elegantly/laravel-impersonator)
[![Tests](https://github.com/ElegantEngineeringTech/laravel-impersonator/actions/workflows/run-tests.yml/badge.svg)](https://github.com/ElegantEngineeringTech/laravel-impersonator/actions/workflows/run-tests.yml)
[![Laravel Pint](https://github.com/ElegantEngineeringTech/laravel-impersonator/actions/workflows/pint.yml/badge.svg)](https://github.com/ElegantEngineeringTech/laravel-impersonator/actions/workflows/pint.yml)
[![PHPStan](https://github.com/ElegantEngineeringTech/laravel-impersonator/actions/workflows/phpstan.yml/badge.svg)](https://github.com/ElegantEngineeringTech/laravel-impersonator/actions/workflows/phpstan.yml)

Laravel Impersonator provides simple, session-based user impersonation. It remembers the original authenticated user, logs in as another user, and restores the original user when the impersonation ends.

## Installation

Install the package with Composer:

```bash
composer require elegantly/laravel-impersonator
```

The service provider and facade are discovered automatically by Laravel.

## Authorization

Implement the `Impersonate` interface on your user model:

```php
use Elegantly\Impersonator\Impersonate;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable implements Impersonate
{
    public function canImpersonate($user): bool
    {
        return $this->is_admin;
    }

    public function canBeImpersonatedBy($user): bool
    {
        return ! $this->is_admin;
    }
}
```

The package registers an `impersonate` Gate. It allows the action only when the original user can impersonate and the target user can be impersonated.

## Routes

Add authenticated routes using the provided controller:

```php
use Elegantly\Impersonator\Http\Controllers\ImpersonatorController;
use Illuminate\Support\Facades\Route;

Route::middleware('auth')->group(function () {
    Route::post('/impersonate/{user}', [ImpersonatorController::class, 'take'])
        ->name('impersonate.take');

    Route::delete('/impersonate', [ImpersonatorController::class, 'leave'])
        ->name('impersonate.leave');
});
```

The controller authorizes the request, starts or stops impersonation, and redirects back.

## Facade

You may also manage impersonation directly. Authorize the target before starting:

```php
use Elegantly\Impersonator\Facades\Impersonator;
use Illuminate\Support\Facades\Gate;

Gate::authorize('impersonate', $user);
Impersonator::take($user);

Impersonator::isImpersonating(); // bool
Impersonator::getImpersonator(); // original user or null
Impersonator::getImpersonatorId(); // original user ID or null

Impersonator::leave();
```

## Testing

```bash
composer test
```

## Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information on what has changed recently.

## Contributing

Please see [CONTRIBUTING](CONTRIBUTING.md) for details.

## Security Vulnerabilities

Please review [our security policy](../../security/policy) on how to report security vulnerabilities.

## Credits

- [Quentin Gabriele](https://github.com/QuentinGab)
- [All Contributors](../../contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
