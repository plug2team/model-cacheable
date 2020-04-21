<?php


namespace Plug2Team\ModelCached\Concerns;


trait Utils
{
    /**
     * @param string $key
     * @param string $value
     * @return bool
     */
    public function exists(string $key, string $value): bool
    {
        return in_array($value, $this->store($key));
    }

    /**
     * @param string $key
     * @param string $value
     * @return mixed
     */
    public function add(string $key, string $value)
    {
        if($this->exists($key, $value)) return;

        $items = $this->store($key);

        array_push($items, $value);

        $this->cache->put($key, $items);
    }

    /**
     * @param string $key
     * @return array
     */
    public function store(string $key): array
    {
        return $this->cache->get($key) ?? [];
    }

    /**
     * @param string $key
     * @return mixed
     */
    public function clear(string $key)
    {
        return $this->cache->forget($key);
    }

    /**
     * @param string $template
     * @param mixed ...$args
     * @return string
     */
    public function resolveCacheName(string $template, ...$args): string
    {
        $base_name = config('model_cached.cache_name');
        //
        return sprintf("{$base_name}.{$template}", ...$args);
    }
}
