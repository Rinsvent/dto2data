<?php

namespace Rinsvent\DTO2Data\Tests\unit\Converter\fixtures\FillTest;

use Rinsvent\DTO2Data\Tests\unit\Converter\fixtures\FillTest\Transformer\ClassData;

#[ClassData]
class HelloClassTransformersRequest
{
    public string $surname;
    public int $age;
}
