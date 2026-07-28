<?php

namespace Dgiftedx\VisitorLogger\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Dgiftedx\VisitorLogger\Models\VisitorLog;

class FingerprintController extends Controller
{
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'fingerprint' => ['required', 'string'],
        ]);

        $sessionKey = config('visitor-logger.session_key');
        $logId = $request->session()->get($sessionKey);

        if (!$logId) {
            return response()->json(['status' => 'error', 'message' => 'No active visitor log session.'], 422);
        }

        $log = VisitorLog::find($logId);

        if (!$log) {
            return response()->json(['status' => 'error', 'message' => 'Visitor log not found.'], 404);
        }

        $log->update(['device_fingerprint' => $validated['fingerprint']]);

        $request->session()->forget($sessionKey);

        return response()->json(['status' => 'ok']);
    }
}
