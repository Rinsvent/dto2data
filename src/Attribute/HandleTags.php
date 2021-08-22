<?php

namespace Rinsvent\DTO2Data\Attribute;

#[\Attribute(\Attribute::TARGET_ALL|\Attribute::IS_REPEATABLE)]
class HandleTags
{
    public function __construct(
        public string $method,
    ) {}
}
