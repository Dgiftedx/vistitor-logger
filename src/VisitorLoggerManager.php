<?php

namespace Dgiftedx\VisitorLogger;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Carbon;
use Dgiftedx\VisitorLogger\Models\VisitorLog;

class VisitorLoggerManager
{
    public function query(): Builder
    {
        return VisitorLog::query();
    }

    public function recent(int $limit = 20): \Illuminate\Database\Eloquent\Collection
    {
        return $this->query()->latest()->limit($limit)->get();
    }

    public function fromCountry(string $country): \Illuminate\Database\Eloquent\Collection
    {
        return $this->query()->where('country', $country)->latest()->get();
    }

    public function fromBrowser(string $browser): \Illuminate\Database\Eloquent\Collection
    {
        return $this->query()->where('browser', $browser)->latest()->get();
    }

    public function withFingerprint(): \Illuminate\Database\Eloquent\Collection
    {
        return $this->query()->whereNotNull('device_fingerprint')->latest()->get();
    }

    public function today(): \Illuminate\Database\Eloquent\Collection
    {
        return $this->query()->whereDate('created_at', Carbon::today())->latest()->get();
    }

    public function statsByBrowser(int $limit = 5): \Illuminate\Support\Collection
    {
        return $this->query()
            ->selectRaw('browser, count(*) as visits')
            ->groupBy('browser')
            ->orderByDesc('visits')
            ->limit($limit)
            ->get();
    }

    public function count(): int
    {
        return $this->query()->count();
    }
}
