<?php

namespace App\Observers;

use Spatie\ResponseCache\Facades\ResponseCache;

class CacheClearObserver
{
    public function saved($model): void
    {
        ResponseCache::clear();
    }

    public function deleted($model): void
    {
        ResponseCache::clear();
    }
}
