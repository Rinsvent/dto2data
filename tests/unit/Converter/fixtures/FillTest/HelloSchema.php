<?php

namespace Rinsvent\DTO2Data\Tests\unit\Converter\fixtures\FillTest;

use Rinsvent\DTO2Data\Attribute\Schema;

#[\Attribute(\Attribute::TARGET_ALL|\Attribute::IS_REPEATABLE)]
class HelloSchema extends Schema
{
    public ?array $baseMap = [
        'surname',
        'fake_age' => 'age',
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
        'createdAt',

        'psurname',
        'pfake_age' => 'page',
        'pemails',
        'pauthors' => [
            'name',
        ],
        'pauthors2' => [
            'name',
        ],
        'pauthors3',
        'pbuy' => [
            'phrase',
            'length',
            'isFirst',
        ],
        'pbar' => [
            'barField'
        ],
        'puuid',
        'pcollection' => [
            'value'
        ],
        'pcreatedAt',

        'fake_Pdevdo' => 'pdevdo'
    ];
}
