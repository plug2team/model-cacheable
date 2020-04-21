<?php


namespace Plug2Team\ModelCached;


use Illuminate\Cache\RedisTaggedCache;

class Strategy
{
    public ?RedisTaggedCache $cache;

    private string $tag;

    private array $groups = [];

    /**
     * Strategy constructor.
     * @param string $tag
     */
    public function __construct(string $tag)
    {
        $this->tag = $tag;
        $this->resolveCache();
    }

    /**
     * @return void
     */
    private function resolveCache(): void
    {
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
     * @return Group
     */
    public function getGroup(string $name)
    {
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
        $list = $this->getIndexs();
        dump($index);
        if(in_array($index, $list)) return;

        array_push($list, $index);

        $this->cache->put('index', $list);
    }

    /**
     * Get index
     *
     * @return array
     */
    public function getIndexs() : array
    {
        return $this->cache->get('index') ?? [];
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
     * @return string
     */
    public function getTag(): string
    {
        return $this->tag;
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
