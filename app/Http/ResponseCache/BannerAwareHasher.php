<?php

namespace App\Http\ResponseCache;

use Illuminate\Http\Request;
use Spatie\ResponseCache\Hasher\DefaultHasher;

class BannerAwareHasher extends DefaultHasher
{
    public function getHashFor(Request $request): string
    {
        $bannerDismissed = $request->cookie('banner_dismissed') ? '1' : '0';

        return 'responsecache-' . md5(
            "{$request->getUri()}-{$request->getMethod()}/{$bannerDismissed}-{$this->cacheProfile->useCacheNameSuffix($request)}"
        );
    }
}
