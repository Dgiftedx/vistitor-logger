<?php

namespace Dgiftedx\VisitorLogger\Middleware;

use Closure;
use Illuminate\Http\Request;
use Jenssegers\Agent\Agent;
use Dgiftedx\VisitorLogger\Jobs\EnrichVisitorLog;
use Dgiftedx\VisitorLogger\Models\VisitorLog;

class LogVisitorData
{
    public function handle(Request $request, Closure $next): mixed
    {
        if (!config('visitor-logger.enabled')) {
            return $next($request);
        }

        $ip = $request->ip();

        if (in_array($ip, config('visitor-logger.exclude_ips', []))) {
            return $next($request);
        }

        $agent = new Agent();
        $agent->setUserAgent($request->userAgent());

        $deviceType = 'desktop';
        if ($agent->isMobile()) {
            $deviceType = 'mobile';
        } elseif ($agent->isTablet()) {
            $deviceType = 'tablet';
        }

        $log = VisitorLog::create([
            'ip_address'         => $ip,
            'user_agent'         => $request->userAgent(),
            'browser'            => $agent->browser(),
            'browser_version'    => $agent->version($agent->browser()),
            'platform'           => $agent->platform(),
            'device_type'        => $deviceType,
            'device_fingerprint' => null,
            'referer'            => $request->header('referer'),
            'url'                => $request->fullUrl(),
            'session_id'         => $request->session()->getId(),
        ]);

        $request->session()->put(config('visitor-logger.session_key'), $log->id);
        $request->session()->save();

        if (config('visitor-logger.queue_enrich')) {
            EnrichVisitorLog::dispatch($log->id, $ip)
                ->onConnection(config('visitor-logger.queue_connection'));
        }

        return $next($request);
    }
}
