<?php

use Dgiftedx\VisitorLogger\Http\Controllers\FingerprintController;
use Illuminate\Support\Facades\Route;

Route::post(config('visitor-logger.fingerprint_route'), [FingerprintController::class, 'store'])
    ->name('visitor-logger.fingerprint');
