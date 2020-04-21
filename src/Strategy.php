<?php


namespace Plug2Team\ModelCached;


use Illuminate\Cache\RedisTaggedCache;

class Strategy
{
    public ?RedisTaggedCache $cache;

    private string $tag;

    private array $groups = [];

    private array $models = [];

    /**
     * @param string $model
     */
    public function __construct($model)
    {
        $this->tag = cacheable_tag_name($model);
        $this->resolveCache($model);
    }

    /**
     * @param string $model
     */
    private function resolveCache(string $model): void
    {
        $models = app('cache')->get('model_cached.models') ?? [];

        if(!in_array($model, $models)) {
            array_push($models, $model);
            app('cache')->put('model_cached.models', $models);
        }
        $this->cache = app('cache')->tags($this->tag);
    }

    /**
     * @param string $name
     * @param array $indexes
     * @return $this
     */
    public function addGroup(string $name, array $indexes = [])
    {
        $group = new Group($name, $this);
        $group->setIndexes($indexes);

        $this->groups[$name] = $group;

        return $this;
    }

    /**
     * @param string $name
     * @return bool
     */
    public function hasGroup(string $name): bool
    {
        return isset($this->groups[$name]);
    }

    /**
     * @param string $name
     * @return mixed
     * @throws \Throwable
     */
    public function group(string $name)
    {
        throw_if(!$this->hasGroup($name),  new \InvalidArgumentException('group not register.'));

        return $this->groups[$name];
    }

    /**
     * Persist item
     *
     * @param $item
     * @param string $key
     */
    public function persist($item, string $key)
    {
        $this->createIndex($key);

        $this->cache->put($key, $item);
    }

    /**
     * Get item
     *
     * @param string $key
     * @return mixed
     */
    public function retrieve(string  $key)
    {
        return $this->cache->get($key);
    }

    /**
     * Remove item
     *
     * @param $key
     * @return bool
     */
    public function forget($key)
    {
        return $this->cache->forget($key);
    }

    /**
     * Add index
     *
     * @param $index
     */
    public function createIndex($index)
    {
        $list = $this->getIndexes();

        if(in_array($index, $list)) return;

        array_push($list, $index);

        $this->cache->put('index', $list);
    }

    /**
     * Clean items
     *
     * @return void
     */
    public function flush()
    {
        $this->cache->flush();
    }

    /**
     * Get index
     *
     * @return array
     */
    public function getIndexes() : array
    {
        return $this->cache->get('index') ?? [];
    }

    /**
     * @return string
     */
    public function getTag(): string
    {
        return $this->tag;
    }

    /**
     * @return array
     */
    public function getGroups(): array
    {
        return $this->groups;
    }

    /**
     * Get cache key
     *
     * @param $key
     * @param mixed ...$args
     * @return string
     */
    public function resolveCacheKey($key, ...$args): string
    {
        return sprintf("{$this->tag}.{$key}", ...$args);
    }
}
