<?php


namespace Plug2Team\ModelCacheable;


use Illuminate\Support\Collection;

class Group
{
    private string $name;

    private array $indexes = [];

    private Index $index;

    /**
     * Group constructor.
     * @param string $name
     * @param Index $index
     */
    public function __construct(string $name, Index $index)
    {
        $this->name = $name;
        $this->index = $index;
    }

    /**
     * Add item to persistence
     *
     * @param $index
     */
    public function persist($index)
    {
        # $this->index->add($this->getName(), $index);
    }

    /**
     * Retrieve elements in group
     *
     * @return Collection
     */
    public function retrieve(): Collection
    {
        $items = [];

        foreach ($this->index->store($this->getName()) as $index) {
            $items[] = $this->index->get($index);
        }

        return collect(array_filter($items));
    }

    /**
     * Clear indexes group
     *
     * @return void
     */
    public function flush()
    {
        $this->index->clear($this->getName());
    }

    public function store()
    {
        return collect($this->index->store($this->getName()));
    }

    /**
     * Get group name
     *
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * Get indexes in group
     *
     * @return array
     */
    public function getIndexes(): array
    {
        return $this->indexes;
    }

    /**
     * Set indexes in group
     *
     * @param array $indexes
     * @return Group
     */
    public function setIndexes(array $indexes): Group
    {
        $this->indexes = $indexes;

        $this->index->addMany($this->getName(), $indexes);

        return $this;
    }
}
