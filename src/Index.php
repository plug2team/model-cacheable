<?php


namespace Plug2Team\ModelCacheable;


use Illuminate\Cache\CacheManager;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Plug2Team\ModelCacheable\Concerns\TTL;
use Plug2Team\ModelCacheable\Concerns\Utils;

class Index
{
    use Utils, TTL;

    private string $tag;

    private ?string $model;

    private array $groups = [];

    private CacheManager $cache;

    /**
     * Index constructor.
     * @param Strategy $strategy
     * @param string $tag
     * @param string|null $model
     */
    public function __construct(Strategy $strategy, string $tag, ?string $model)
    {
        $this->tag = $tag;
        $this->model = $model;
        $this->cache = $strategy->cache;
    }

    /**
     * Get tag name
     *
     * @return string
     */
    public function getTag(): string
    {
        return $this->tag;
    }

    /**
     * Heap of indexes
     *
     * @return Collection
     */
    public function heap(): Collection
    {
        return collect($this->store($this->getIndexCacheName()));
    }

    public function pushAll($values)
    {
        $this->cache->put($this->getIndexCacheName(), $values);
    }

    /**
     * Add index in heap
     *
     * @param $index
     */
    public function push($index): void
    {
        $this->add($this->getIndexCacheName(), $index);
    }

    /**
     * Set item
     *
     * @param $item
     */
    public function set($item) : void
    {
        $this->cache->put($this->getItemCacheName($item->id), $item);

        $this->push($item->id);
    }

    /**
     * Retrieve item by key
     *
     * @param $key
     * @return mixed
     */
    public function get($key)
    {
        return $this->cache->get($this->getItemCacheName($key));
    }

    /**
     * Remove item to heap
     *
     * @param $key
     * @return mixed
     */
    public function forget($key)
    {
        $heap = $this->heap();

        // get index position
        $index = $heap->search($key);

        if($index) {
            // clear indexes
            $this->clear($this->getIndexCacheName());

            // remove index
            $heap->splice($index, 1);

            // re-sync id list
            $heap->each(fn($id) => $this->push($id));
        }

        return $this->clear($this->getItemCacheName($key));
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
     * Get group by name
     *
     * @param string $key
     * @return Group|null
     */
    public function group(string $key): ?Group
    {
        return $this->groups[$key];
    }

    /**
     * Add group to index
     *
     * @param string $key
     * @param \Closure|array $value
     * @return Index
     */
    public function addGroup(string $key, $value): Index
    {
        $indexes = is_callable($value) ? $this->prepareCallable($value) : $value;

        $group = new Group($this->getItemCacheName($key), $this);
        $group->setIndexes($indexes);

        $this->groups[$key] = $group;

        return $this;
    }

    /**
     * Get all groups
     *
     * @return array
     */
    public function getGroups() : array
    {
        return $this->groups;
    }

    /**
     * Get index key
     *
     * @return string
     */
    public function getIndexCacheName()
    {
        return $this->resolveCacheName('%s.%s', $this->getTag(), 'indexes');
    }

    /**
     * Get name by key
     *
     * @param $key
     * @return string
     */
    public function getItemCacheName($key)
    {
        return $this->resolveCacheName('%s.%s', $this->getTag(), $key);
    }

    /**
     * @param callable $fnc
     * @return array
     */
    private function prepareCallable(callable $fnc): array
    {
        $call = call_user_func($fnc, resolve($this->model));

        $indexes = [];

        if(!$call instanceof Builder) {
            return $indexes;
        }

        $indexes = $call->select('id')->get()->pluck('id')->toArray();

        return  $indexes;
    }
}
