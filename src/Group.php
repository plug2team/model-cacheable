<?php


namespace Plug2Team\ModelCached;


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
     * @param $index
     */
    public function persist($index)
    {
        $this->index->add($this->getName(), $index);
    }

    /**
     * @return Collection
     */
    public function retrieve(): Collection
    {
        $items = [];

        foreach ($this->index->store($this->getName()) as $index) {
            $items[] = $this->index->get($index);
        }

        return collect($items);
    }

    /**
     * clear indexes group;
     *
     * @return void
     */
    public function flush()
    {
        dd($this->getName());
//        $this->index->forgetStore($this->getName());
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
