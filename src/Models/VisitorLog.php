<?php

namespace Dgiftedx\VisitorLogger\Models;

use Illuminate\Database\Eloquent\Model;

class VisitorLog extends Model
{
    protected $table = 'visitor_logs';

    protected $fillable = [
        'ip_address',
        'user_agent',
        'browser',
        'browser_version',
        'platform',
        'device_type',
        'device_fingerprint',
        'latitude',
        'longitude',
        'country',
        'city',
        'referer',
        'url',
        'session_id',
    ];
}
