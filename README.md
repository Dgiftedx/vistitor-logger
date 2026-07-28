# Visitor Logger for Laravel

Log visitor data including IP, device fingerprint, browser, platform, and geolocation in Laravel effortlessly.

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

Add FingerprintJS from the CDN and the published asset inside your `<head>` or before `</body>`. This is required if you want to track the device fingerprint:

```html
<script src="https://openfpcdn.io/fingerprintjs/v4"></script>
<script src="{{ asset('vendor/visitor-logger.js') }}"></script>
```

> **Note**: The package automatically registers its middleware globally to log every request. You don't need to manually add it to your `Kernel.php` or `bootstrap/app.php`.

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

When `queue_enrich` is set to true (default), geolocation data (latitude, longitude, country, city) is fetched in the background via queued jobs. **Ensure your queue worker is running** (`php artisan queue:work`) to process these jobs.

### MaxMind (default)

Download a [GeoLite2-City database](https://dev.maxmind.com/geoip/geolite2-free-geolocation-data) and place it at `storage/app/GeoLite2-City.mmdb` (or update `maxmind_database_path` in config).

### ip-api

Set `geolocation_driver` to `ipapi` in config. No API key is required for the free tier.

## Usage

Use the `VisitorLogger` facade anywhere in your application:

```php
use Dgiftedx\VisitorLogger\Facades\VisitorLogger;

// Latest logs (default limit: 20)
VisitorLogger::recent();

// Latest logs with custom limit
VisitorLogger::recent(50);

// Logs from a specific country
VisitorLogger::fromCountry('United States');

// Logs from a specific browser
VisitorLogger::fromBrowser('Chrome');

// Logs with a fingerprint recorded
VisitorLogger::withFingerprint();

// Logs from today
VisitorLogger::today();

// Top browsers by visit count (default limit: 5)
VisitorLogger::statsByBrowser();

// Top browsers by visit count with custom limit
VisitorLogger::statsByBrowser(10);

// Total log count
VisitorLogger::count();

// Raw query builder for custom queries (e.g., filter by device_type)
VisitorLogger::query()->where('device_type', 'mobile')->get();
```

### The VisitorLog Model

You can also interact directly with the Eloquent model: `Dgiftedx\VisitorLogger\Models\VisitorLog`.
The following attributes are logged and available on the model:

- `ip_address`
- `user_agent`
- `browser`
- `browser_version`
- `platform` (e.g., Windows, OS X, Linux)
- `device_type` (`desktop`, `mobile`, `tablet`)
- `device_fingerprint`
- `latitude`
- `longitude`
- `country`
- `city`
- `referer`
- `url`
- `session_id`

```php
use Dgiftedx\VisitorLogger\Models\VisitorLog;

// Example: Get all mobile visitors from a specific city
$mobileLogs = VisitorLog::where('device_type', 'mobile')
    ->where('city', 'New York')
    ->get();
```
