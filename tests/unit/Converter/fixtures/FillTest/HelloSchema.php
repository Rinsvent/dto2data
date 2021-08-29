<?php

namespace Rinsvent\DTO2Data\Tests\unit\Converter\fixtures\FillTest;

use Rinsvent\DTO2Data\Attribute\Schema;

#[\Attribute(\Attribute::TARGET_ALL|\Attribute::IS_REPEATABLE)]
class HelloSchema extends Schema
{
    public ?array $baseMap = [
        'surname',
        'age',
        'emails',
        'authors' => [
            'name',
        ],
        'authors2' => [
            'name',
        ],
        'authors3',
        'buy' => [
            'phrase',
            'length',
            'isFirst',
        ],
        'bar' => [
            'barField'
        ],
        'uuid',
        'collection' => [
            'value'
        ],
    ];
}
