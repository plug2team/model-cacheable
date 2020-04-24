<?php


namespace Plug2Team\ModelCacheable\Concerns;


use Carbon\Carbon;

trait TTL
{
    /**
     * @return bool|Carbon
     */
    public function getTTL()
    {
        if(config('model_cached.tag.permanent')) return false;

        return Carbon::now()->addMinutes(config('model_cached.tag.time'));
    }
}
