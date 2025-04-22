# Laravel Events

[![Latest Version on Packagist](https://img.shields.io/packagist/v/juniorfontenele/laravel-events.svg?style=flat-square)](https://packagist.org/packages/juniorfontenele/laravel-events)
[![Tests](https://img.shields.io/github/actions/workflow/status/juniorfontenele/laravel-events/tests.yml?branch=main&label=tests&style=flat-square)](https://github.com/juniorfontenele/laravel-events/actions/workflows/tests.yml)
[![Total Downloads](https://img.shields.io/packagist/dt/juniorfontenele/laravel-events.svg?style=flat-square)](https://packagist.org/packages/juniorfontenele/laravel-events)

Laravel Events is a package designed to simplify event handling in Laravel applications. It provides tools for logging, rate-limiting, and syncing events between files and the database.

## Features

- Log events to files or the database.
- Rate-limit event handling to prevent abuse.
- Sync events between files and the database.
- Support for custom event types and levels.
- Automatically generates unique and sequential identifiers for events.
- Includes database migrations for event storage.

## Installation

You can install the package via composer:

```bash
composer require juniorfontenele/laravel-events
```

After installation, run the migrations to set up the necessary database tables:

```bash
php artisan migrate
```

If you want to customize the migrations, you can publish them using the following command:

```bash
php artisan vendor:publish --tag=laravel-events-migrations
```

The migrations will be published to the `database/migrations` directory, where you can modify them as needed.

## Configuration

Publish the configuration file to customize the package behavior:

```bash
php artisan vendor:publish --tag=laravel-events-config
```

The configuration file will be published to `config/events.php`. You can customize settings such as rate-limiting and default event behavior.

## Usage

### Creating Events

Create events using the Artisan command provided by the package:

```bash
php artisan make:event EventName
```

This command will generate a new event file in the appropriate directory with a unique and sequential identifier for traceability. The generated file will include all necessary properties, such as `id`, `name`, `type`, `level`, and `description`.

Example of a generated event file:

```php
use JuniorFontenele\LaravelEvents\BaseEvent;
use JuniorFontenele\LaravelEvents\Enums\EventType;
use JuniorFontenele\LaravelEvents\Enums\EventLevel;

class UserRegistered extends BaseEvent
{
    public int $id = 1; // Automatically generated
    public string $name = 'User Registered';
    public EventType $type = EventType::USER;
    public EventLevel $level = EventLevel::INFO;
    public string $description = 'Triggered when a user registers.';
    public bool $shouldLog = true;
    public bool $shouldWriteToDatabase = true;
}
```

### Logging Events

Use the `WriteEventsToLog` listener to log events to the Laravel log system. Ensure your event implements the `shouldLog` method.

### Writing Events to the Database

Use the `WriteEventsToDatabase` listener to persist events to the database. Ensure your event implements the `shouldWriteToDatabase` method.

### Rate Limiting

The `HasRateLimiter` trait provides methods to limit the number of times an event can be handled within a specific time frame. Configure rate-limiting in `config/events.php`.

### Commands

The package provides several Artisan commands:

#### Sync Events from Files

```bash
php artisan events:sync-from-file
```

This command updates the database with the information from the event files located in the `App\Events` directory. It ensures that all events defined in files are properly registered in the database. This is useful when new events are added or existing ones are modified in the codebase.

#### Sync Events from Database

```bash
php artisan events:sync-from-db
```

This command generates event files for events that exist in the database but are missing in the `App\Events` directory. It ensures that all events in the database have corresponding files in the codebase. This is useful for restoring missing event files.

#### Clear Old Events

```bash
php artisan events:clear-old
```

This command deletes events older than a specified number of days (default is 180 days). The maximum age can be configured in `config/events.php` under the `max_days` setting. This command can be added to the Laravel scheduler to automatically clean up old event records:

```php
$schedule->command('events:clear-old')->daily();
```

#### Reorder Event IDs

```bash
php artisan events:reorder-ids
```

This command reorders the event IDs in the database to ensure they are sequential. This is useful when events have been deleted, and you want to maintain a clean, sequential order of IDs. Use the `--force` option to skip confirmation:

```bash
php artisan events:reorder-ids --force
```

#### List Events

```bash
php artisan events:list
```

List all events stored in the database.

## Testing

Run the tests with:

```bash
composer test
```

## Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information on what has changed recently.

## Contributing

Please see [CONTRIBUTING](https://github.com/spatie/.github/blob/main/CONTRIBUTING.md) for details.

## Security Vulnerabilities

Please review [our security policy](../../security/policy) on how to report security vulnerabilities.

## Credits

- [Junior Fontenele](https://github.com/juniorfontenele)
- [All Contributors](../../contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
