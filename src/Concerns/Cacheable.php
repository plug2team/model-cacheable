<?php


namespace Plug2Team\ModelCached\Concerns;

use Illuminate\Support\Str;
use phpDocumentor\Reflection\Types\Self_;
use Plug2Team\ModelCached\Strategy;

trait Cacheable
{
    private static array $watchModelEvents = [
        'created',
        'updated',
        'deleted',
        'retrieved',
    ];

    public static ?Strategy $strategy = null;

    /**
     * @return void
     */
    protected static function bootCacheable() : void
    {
        $cache_tag = cacheable_tag_name(__CLASS__);

        static::$strategy = app(Strategy::class, ['tag' => $cache_tag]);

        foreach (static::$watchModelEvents as $event) {
            $method_name = "__{$event}";

            if(!method_exists(static::class, $method_name)) continue;

            static::registerModelEvent($event, __CLASS__ . "@{$method_name}");
        }

        // register group default
        static::$strategy->addGroup('all', static::$strategy->getIndexs());
    }

    /**
     * @param $model
     */
    public function __created($model)
    {
        $cache_key = static::$strategy->resolveCacheKey("%s", $model->id);

        static::$strategy->persist($model, $cache_key);
    }

    /**
     * @param $model
     */
    public function __updated($model)
    {
        $cache_key = static::$strategy->resolveCacheKey("%s", $model->id);

        static::$strategy->forget($cache_key);

        static::$strategy->persist($model, $cache_key);
    }

    /**
     * @param $model
     */
    public function __deleted($model)
    {
        $cache_key = static::$strategy->resolveCacheKey("%s", $model->id);

        static::$strategy->forget($cache_key);
    }
}
