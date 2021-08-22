<?php

namespace Rinsvent\DTO2Data\Tests\unit\Converter\fixtures\FillTest;

use Rinsvent\DTO2Data\Attribute\Schema;
use Rinsvent\DTO2Data\DTO\Map;

#[\Attribute(\Attribute::TARGET_ALL|\Attribute::IS_REPEATABLE)]
class HelloSchema extends Schema
{
    public function __construct(
    ) {

        parent::__construct(
            (new Map())
                ->addProperty('surname')
                ->addProperty('age')
                ->addProperty('emails')
                ->addProperty(
                    'authors',
                    (new Map())
                        ->addProperty('name')
                )
                ->addProperty('buy',
                    (new Map)
                        ->addProperty('phrase')
                        ->addProperty('length')
                        ->addProperty('isFirst')
                )
                ->addProperty('bar',
                    (new Map())
                        ->addProperty('barField')
                )
            ,
            ['default']
        );
    }
}