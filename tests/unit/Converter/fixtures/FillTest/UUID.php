<?php

namespace Rinsvent\DTO2Data\Tests\unit\Converter\fixtures\FillTest;

class UUID
{
    public string $id;
    public int $version = 4;

    public function __construct(string $id)
    {
        $this->id = $id;
    }

    public function __toString(): string
    {
        return $this->id;
    }
}
