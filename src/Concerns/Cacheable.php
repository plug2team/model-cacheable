<?php


namespace Plug2Team\ModelCacheable\Concerns;

use Plug2Team\ModelCacheable\Index;
use Throwable;

trait Cacheable
{
    protected static ?Index $strategy;

    /**
     * inform which model is curly
     *
     * @return void
     */
    public static function crape(): void
    {
        app('cacheable')->register(__CLASS__);

        static::$strategy = app('cacheable')->index(__CLASS__);

        // push indexes
        static::$strategy->pushAll(static::all('id')->pluck('id')->toArray());

        // register group default
        static::$strategy->addGroup('all', static::$strategy->heap()->toArray());
    }

    /**
     * Observer events
     *
     * @return void
     */
    protected static function bootCacheable() : void
    {
        static::registerModelEvent('saved', __CLASS__ . "@cachePersist");
        static::registerModelEvent('deleted', __CLASS__ . "@cacheClear");
        # static::registerModelEvent('retrieved', __CLASS__ . "@cacheRetrieve");
    }

    /**
     * @param $model
     */
    public function cacheRetrieve($model)
    {
        // ignore if exist id in heap.
        if(self::$strategy->heap()->contains($model->id)) return;

        static::$strategy->push($model->id);
    }

    /**
     * @param $model
     */
    public function cachePersist($model)
    {
        static::$strategy->forget($model->id);

        static::$strategy->set($model);
    }

    /**
     * @param $model
     */
    public function cacheClear($model)
    {
        static::$strategy->forget($model->id);
    }

    /**
     * @param string $group_name
     * @return mixed
     * @throws Throwable
     */
    public static function cache(string $group_name = 'all')
    {
        return static::$strategy->group($group_name)->retrieve();
    }

    /**
     * Using in collection each->cached()
     *
     * @return void
     */
    public function cached(): void
    {
        static::$strategy->set($this);
    }

    /**
     * @return Index|null
     */
    public static function getStrategy(): ?Index
    {
        return static::$strategy;
    }
}
