<?php


namespace Plug2Team\ModelCached\Concerns;

use Illuminate\Support\Collection;
use Plug2Team\ModelCached\Strategy;
use Throwable;

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
        static::$strategy = app(Strategy::class, ['model' => __CLASS__]);

        foreach (static::$watchModelEvents as $event) {
            $method_name = "__{$event}";

            if(!method_exists(static::class, $method_name)) continue;

            static::registerModelEvent($event, __CLASS__ . "@{$method_name}");
        }

        // register group default
        static::$strategy->addGroup('all', static::$strategy->getIndexes());

//        Collection::macro('persist', function($name) {
//            dd($name);
//        });
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

    /**
     * @param string $group_name
     * @return mixed
     * @throws Throwable
     */
    public static function loadCache(string $group_name = 'all')
    {
        return static::$strategy->group($group_name)->retrieve();
    }

    /**
     * Cache item
     *
     * @return void
     */
    public function cached(): void
    {
        $cache_key = static::$strategy->resolveCacheKey("%s", $this->id);

        static::$strategy->persist($this, $cache_key);
    }
}
