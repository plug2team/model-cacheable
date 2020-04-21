<?php


namespace Plug2Team\ModelCached;


use Illuminate\Cache\CacheManager;
use Plug2Team\ModelCached\Concerns\TTL;
use Plug2Team\ModelCached\Concerns\Utils;

class Index
{
    use Utils, TTL;

    private string $tag;

    private array $groups = [];

    private CacheManager $cache;

    /**
     * Index constructor.
     * @param Strategy $strategy
     * @param string $tag
     */
    public function __construct(Strategy $strategy, string $tag)
    {
        $this->tag = $tag;
        $this->cache = $strategy->cache;
    }

    /**
     * @return string
     */
    public function getTag(): string
    {
        return $this->tag;
    }

    /**
     * Flush cache entries
     *
     * @return void
     */
    public function flush(): void
    {
        $tag_index = $this->getIndexCacheName();

        foreach ($this->store($tag_index) as $key) {
            $this->clear($this->getItemCacheName($key));
        }

        foreach ($this->getGroups() as $key => $group) {
            $this->clear($this->getItemCacheName($key));
        }

        $this->cache->forget($tag_index);
    }

    /**
     * @return array
     */
    public function all(): array
    {
        return $this->store($this->getIndexCacheName());
    }

    /**
     * @param $index
     */
    public function push($index): void
    {
        $this->add($this->getIndexCacheName(), $index);
    }

    /**
     * @param $item
     */
    public function set($item) : void
    {
        $this->cache->put($this->getItemCacheName($item->id), $item);

        $this->push($item->id);
    }

    /**
     * @param $key
     * @return mixed
     */
    public function get($key)
    {
        return $this->cache->get($this->getItemCacheName($key));
    }

    /**
     * @param $key
     * @return mixed
     */
    public function forget($key)
    {
        return $this->clear($this->getItemCacheName($key));
    }

    /**
     * @param string $key
     * @return Group|null
     */
    public function group(string $key): ?Group
    {
        return $this->groups[$key];
    }

    /**
     * @param string $key
     * @param array $indexes
     * @return Index
     */
    public function addGroup(string $key, array $indexes = []): Index
    {
        $group = new Group($this->getItemCacheName($key), $this);
        $group->setIndexes($indexes);

        $this->groups[$key] = $group;

        return $this;
    }

    /**
     * @return array
     */
    public function getGroups() : array
    {
        return $this->groups;
    }

    /**
     * @return string
     */
    public function getIndexCacheName()
    {
        return $this->resolveCacheName('%s.%s', $this->getTag(), 'indexes');
    }

    /**
     * @param $key
     * @return string
     */
    public function getItemCacheName($key)
    {
        return $this->resolveCacheName('%s.%s', $this->getTag(), $key);
    }
}
