<?php

namespace Rinsvent\DTO2Data\Tests\unit\Converter\fixtures\FillTest;

use Rinsvent\DTO2Data\Attribute\Schema;
use Rinsvent\DTO2Data\DTO\Map;

#[\Attribute(\Attribute::TARGET_ALL|\Attribute::IS_REPEATABLE)]
class HelloSchema extends Schema
{
    public ?array $map = [
        'surname',
        'age',
        'emails',
        'authors' => [
            'name',
        ],
        'buy' => [
            'phrase',
            'length',
            'isFirst',
        ],
        'bar' => [
            'barField'
        ]
    ];
}
