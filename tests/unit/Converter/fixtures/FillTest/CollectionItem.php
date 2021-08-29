<?php

namespace Rinsvent\DTO2Data\Tests\unit\Converter\fixtures\FillTest;

class CollectionItem
{
    public string $value;

    public function __construct(string $value)
    {
        $this->value = $value;
    }
}
