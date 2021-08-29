<?php

namespace Rinsvent\DTO2Data\Tests\unit\Converter\fixtures\FillTest;

class Collection implements \Iterator
{
    public array $items = [];
    private $position = 0;

    public function rewind()
    {
        $this->position = 0;
    }

    public function current()
    {
        return $this->items[$this->position];
    }

    public function key()
    {
        return $this->position;
    }

    public function next()
    {
        ++$this->position;
    }

    public function valid()
    {
        return isset($this->items[$this->position]);
    }
}
