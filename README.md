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
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Foundation\Auth\User as BaseUser;

class User extends BaseUser implements Impersonate
{
    public function canImpersonate(Authenticatable&Impersonate $user): bool
    {
        return $this->is_admin;
    }

    public function canBeImpersonatedBy(Authenticatable&Impersonate $user): bool
    {
        return ! $this->is_admin;
    }
}
```

The package registers an `impersonate` Gate. It prevents self-impersonation and requires both users to approve the relationship through the methods above.

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

## Facade

You may also manage impersonation directly. Authorize the target before starting:

```php
use Elegantly\Impersonator\Facades\Impersonator;
use Illuminate\Support\Facades\Gate;

Gate::authorize('impersonate', $user);
Impersonator::take($user); // bool

Impersonator::isImpersonating(); // bool
Impersonator::getImpersonator(); // original user or null
Impersonator::getImpersonatorId(); // original user ID or null

Impersonator::leave(); // bool
```

`take()` and `leave()` return whether the impersonation state changed. Direct facade calls do not authorize automatically, so always call the `impersonate` Gate before `take()`.

## Events

The package dispatches events after each successful transition:

- `Elegantly\Impersonator\Events\TakeImpersonation`
- `Elegantly\Impersonator\Events\LeaveImpersonation`

Both events expose the original user as `$impersonator` and the other user as `$impersonated`:

```php
use Elegantly\Impersonator\Events\TakeImpersonation;
use Illuminate\Support\Facades\Event;

Event::listen(TakeImpersonation::class, function (TakeImpersonation $event) {
    logger()->info('User impersonated', [
        'impersonator' => $event->impersonator->getAuthIdentifier(),
        'impersonated' => $event->impersonated->getAuthIdentifier(),
    ]);
});
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
