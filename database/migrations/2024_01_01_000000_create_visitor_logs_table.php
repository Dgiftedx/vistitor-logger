<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('visitor_logs', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->string('browser')->nullable();
            $table->string('browser_version')->nullable();
            $table->string('platform')->nullable();
            $table->string('device_type')->nullable();
            $table->string('device_fingerprint')->nullable()->index();
            $table->decimal('latitude', 10, 7)->nullable();
            $table->decimal('longitude', 10, 7)->nullable();
            $table->string('country')->nullable();
            $table->string('city')->nullable();
            $table->string('referer')->nullable();
            $table->string('url')->nullable();
            $table->string('session_id')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('visitor_logs');
    }
};
