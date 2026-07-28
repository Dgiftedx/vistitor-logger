<?php

return [
    'enabled'               => env('VISITOR_LOGGER_ENABLED', true),
    'queue_enrich'          => true,
    'queue_connection'      => null,
    'geolocation_driver'    => 'maxmind_database',
    'maxmind_database_path' => 'app/GeoLite2-City.mmdb',
    'geolocation_api_key'   => env('VISITOR_LOGGER_API_KEY', ''),
    'fingerprint_route'     => '/log-fingerprint',
    'exclude_ips'           => ['127.0.0.1', '::1'],
    'session_key'           => 'visitor_log_id',
];
