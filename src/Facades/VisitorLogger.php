<?php

namespace Dgiftedx\VisitorLogger\Facades;

use Illuminate\Support\Facades\Facade;

class VisitorLogger extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return 'visitor-logger';
    }
}
