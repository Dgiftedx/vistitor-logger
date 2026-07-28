# visitor-logger

Log visitor data including IP, fingerprint, browser & geolocation in Laravel.

## Requirements

- PHP ^8.0
- Laravel 9, 10, or 11

## Installation

### 1. Require the package

```bash
composer require dgiftedx/visitor-logger
```

### 2. Publish config, migrations, and assets

```bash
php artisan vendor:publish --provider="Dgiftedx\VisitorLogger\VisitorLoggerServiceProvider"
```

Or publish individually by tag:

```bash
php artisan vendor:publish --provider="Dgiftedx\VisitorLogger\VisitorLoggerServiceProvider" --tag=config
php artisan vendor:publish --provider="Dgiftedx\VisitorLogger\VisitorLoggerServiceProvider" --tag=migrations
php artisan vendor:publish --provider="Dgiftedx\VisitorLogger\VisitorLoggerServiceProvider" --tag=assets
```

### 3. Run migrations

```bash
php artisan migrate
```

### 4. Include scripts in your layout

Add FingerprintJS from the CDN and the published asset inside your `<head>` or before `</body>`:

```html
<script src="https://openfpcdn.io/fingerprintjs/v4"></script>
<script src="{{ asset('vendor/visitor-logger.js') }}"></script>
```

### 5. (Optional) Custom fingerprint route

If you have changed `fingerprint_route` in the config, expose the route to the script via a global variable **before** loading `visitor-logger.js`:

```html
<script>var visitorLoggerRoute = '{{ config('visitor-logger.fingerprint_route') }}';</script>
<script src="https://openfpcdn.io/fingerprintjs/v4"></script>
<script src="{{ asset('vendor/visitor-logger.js') }}"></script>
```

## Configuration

After publishing, edit `config/visitor-logger.php`:

| Key | Default | Description |
|-----|---------|-------------|
| `enabled` | `true` | Enable or disable logging entirely |
| `queue_enrich` | `true` | Dispatch geolocation enrichment to the queue |
| `queue_connection` | `null` | Queue connection to use (`null` = default) |
| `geolocation_driver` | `maxmind_database` | `maxmind_database` or `ipapi` |
| `maxmind_database_path` | `app/GeoLite2-City.mmdb` | Path relative to `storage_path()` |
| `geolocation_api_key` | `''` | API key (reserved for future drivers) |
| `fingerprint_route` | `/log-fingerprint` | POST route for the JS fingerprint call |
| `exclude_ips` | `['127.0.0.1', '::1']` | IPs to skip logging |
| `session_key` | `visitor_log_id` | Session key used to pass the log ID to JS |

## Geolocation

### MaxMind (default)

Download a [GeoLite2-City database](https://dev.maxmind.com/geoip/geolite2-free-geolocation-data) and place it at `storage/app/GeoLite2-City.mmdb` (or update `maxmind_database_path` in config).

### ip-api

Set `geolocation_driver` to `ipapi` in config. No API key is required for the free tier.

## Usage

Use the `VisitorLogger` facade anywhere in your application:

```php
use Dgiftedx\VisitorLogger\Facades\VisitorLogger;

// Latest 20 logs
VisitorLogger::recent();

// Logs from a specific country
VisitorLogger::fromCountry('United States');

// Logs from a specific browser
VisitorLogger::fromBrowser('Chrome');

// Logs with a fingerprint recorded
VisitorLogger::withFingerprint();

// Logs from today
VisitorLogger::today();

// Top 5 browsers by visit count
VisitorLogger::statsByBrowser();

// Total log count
VisitorLogger::count();

// Raw query builder for custom queries
VisitorLogger::query()->where('platform', 'Windows')->get();
```
