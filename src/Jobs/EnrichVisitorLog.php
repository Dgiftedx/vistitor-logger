<?php

namespace Dgiftedx\VisitorLogger\Jobs;

use GeoIp2\Database\Reader;
use GeoIp2\Exception\AddressNotFoundException;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Http;
use Dgiftedx\VisitorLogger\Models\VisitorLog;

class EnrichVisitorLog implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(
        public int $logId,
        public string $ip,
    ) {}

    public function handle(): void
    {
        $log = VisitorLog::find($this->logId);

        if (!$log) {
            return;
        }

        $location = $this->getLocation($this->ip);

        if ($location !== null) {
            $log->update($location);
        }
    }

    private function getLocation(string $ip): ?array
    {
        $driver = config('visitor-logger.geolocation_driver');

        if ($driver === 'maxmind_database') {
            try {
                $reader = new Reader(storage_path(config('visitor-logger.maxmind_database_path')));
                $record = $reader->city($ip);

                return [
                    'latitude'  => $record->location->latitude,
                    'longitude' => $record->location->longitude,
                    'country'   => $record->country->name,
                    'city'      => $record->city->name,
                ];
            } catch (\Exception $e) {
                return null;
            }
        }

        if ($driver === 'ipapi') {
            try {
                $response = Http::get("http://ip-api.com/json/{$ip}?fields=lat,lon,country,city");

                if (!$response->successful()) {
                    return null;
                }

                $data = $response->json();

                return [
                    'latitude'  => $data['lat'] ?? null,
                    'longitude' => $data['lon'] ?? null,
                    'country'   => $data['country'] ?? null,
                    'city'      => $data['city'] ?? null,
                ];
            } catch (\Exception $e) {
                return null;
            }
        }

        return null;
    }
}
