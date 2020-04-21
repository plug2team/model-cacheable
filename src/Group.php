<?php


namespace Plug2Team\ModelCached;


class Group
{
    private string $name;

    private array $indexes = [];

    protected Strategy $strategy;

    /**
     * Group constructor.
     * @param string $name
     * @param Strategy $strategy
     */
    public function __construct(string $name, Strategy $strategy)
    {
        $this->name = $name;
        $this->strategy = $strategy;
    }

    /**
     * @param string $index
     */
    public function persist(string $index)
    {
        $indexes = $this->strategy->cache->get($this->getName()) ?? [];

        if (in_array($index, $indexes)) return;

        array_push($indexes, $index);

        $this->strategy->cache->put($this->getName(), $indexes);
    }

    /**
     * @return array
     */
    public function retrieve()
    {
        $items = [];

        foreach ($this->strategy->cache->get($this->getName()) as $index) {
            $items[] = $this->strategy->retrieve($index);
        }

        return $items;
    }

    /**
     * clear indexes group;
     *
     * @return void
     */
    public function flush()
    {
        $this->strategy->cache->forget($this->getName());
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @return array
     */
    public function getIndexes(): array
    {
        return $this->indexes;
    }

    /**
     * @param array $indexes
     * @return Group
     */
    public function setIndexes(array $indexes): Group
    {
        $this->indexes = $indexes;

        foreach ($indexes as $index) {
            $this->persist($index);
        }

        return $this;
    }
}
