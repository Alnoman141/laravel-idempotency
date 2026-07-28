# Laravel Idempotency

[![PHP](https://img.shields.io/badge/PHP-8.3%2B-blue.svg)]()
[![Laravel](https://img.shields.io/badge/Laravel-13.x-red.svg)]()
[![License](https://img.shields.io/badge/License-MIT-green.svg)]()

A lightweight and configurable Laravel package that provides **Idempotency-Key** support for APIs. It prevents duplicate requests by safely replaying previously stored responses.

## Features

- 🚀 Laravel 13 Support
- 🔒 Prevent duplicate API requests
- ⚡ Cache & Database storage drivers
- 🔐 Request fingerprint validation
- 🔄 Response replay
- ⏳ Configurable TTL
- 🔒 Concurrency protection using cache locks
- 📢 Events
- 🛠 Artisan commands
- ✅ Pest test support

---

## Requirements

- PHP 8.3+
- Laravel 13.x

---

## Installation

Install via Composer:

```bash
composer require alnoman141/laravel-idempotency
```

---

## Publish Configuration

```bash
php artisan vendor:publish --provider="alnoman141\LaravelIdempotency\IdempotencyServiceProvider" --tag=idempotency-config
```

This publishes:

```
config/idempotency.php
```

---

## Publish Migration

If you are using the **database** driver:

```bash
php artisan vendor:publish --provider="alnoman141\LaravelIdempotency\IdempotencyServiceProvider" --tag=migrations
```

Run the migration:

```bash
php artisan migrate
```

---

## Configuration

Example:

```php
return [

    'driver' => env('IDEMPOTENCY_DRIVER', 'cache'),

    'header' => 'Idempotency-Key',

    'ttl' => 3600,

];
```

Available drivers:

- `cache`
- `database`

---

## Usage

Protect any route using the middleware.

```php
Route::post('/orders', function () {

    return response()->json([
        'success' => true,
    ]);

})->middleware('idempotency');
```

Or specify a custom TTL:

```php
Route::post('/orders', function () {

    //

})->middleware('idempotency:600');
```

---

## Client Example

Include an **Idempotency-Key** header with every request.

```http
POST /api/orders

Idempotency-Key: 550e8400-e29b-41d4-a716-446655440000
```

The same request using the same key will replay the original response instead of executing the request again.

---

## Artisan Commands

Clear stored idempotency records:

```bash
php artisan idempotency:clear
```

View statistics:

```bash
php artisan idempotency:stats
```

Remove expired records:

```bash
php artisan idempotency:prune
```

---

## Events

The package dispatches the following events:

- `IdempotencyStarted`
- `IdempotencyCompleted`
- `IdempotencyReplayed`
- `IdempotencyConflictDetected`
- `IdempotencyFailed`

You can register your own listeners using Laravel's event system.

---

## Testing

Run the test suite:

```bash
composer test
```

or

```bash
vendor/bin/pest
```

---

## Best Practices

- Generate a unique UUID for every write request.
- Never reuse an idempotency key for different payloads.
- Use the database driver for distributed applications.
- Configure a reasonable TTL based on your business requirements.

---

## Contributing

Contributions are welcome! Feel free to submit issues or pull requests.

---

## License

This package is open-sourced software licensed under the **MIT License**.
