<?php

namespace Rinsvent\DTO2Data\Tests\unit\Converter\fixtures\FillTest;

use Rinsvent\DTO2Data\Tests\unit\Converter\fixtures\FillTest\Transformer\ClassObject;

#[ClassObject]
class HelloClassTransformersRequest2
{
    public string $surname;
    public int $age;
}
