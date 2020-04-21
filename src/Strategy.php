<?php


namespace Plug2Team\ModelCached;

use Illuminate\Cache\CacheManager;

class Strategy
{
    public ?CacheManager $cache;

    protected array $models = [];

    public function __construct()
    {
        $this->cache = app('cache');
    }

    /**
     * @param string $class
     */
    public function register(string $class): void
    {
        $tag = cacheable_tag_name($class);

        $this->models[$class] = new Index($this, $tag);
    }

    /**
     * Get models register
     *
     * @return array
     */
    public function getModels(): array
    {
        return $this->models;
    }

    /**
     * @param string $class
     * @return Index
     */
    public function index(string $class) : Index
    {
        return $this->models[$class];
    }
}
