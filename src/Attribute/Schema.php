<?php

namespace Rinsvent\DTO2Data\Attribute;

use Rinsvent\DTO2Data\DTO\Map;

#[\Attribute(\Attribute::TARGET_ALL|\Attribute::IS_REPEATABLE)]
class Schema
{
    public function __construct(
        public ?array $map = null,
        /** @var string[] $tags */
        public array $tags = ['default']
    ) {
        $this->map = $map ?? $this->map;
    }
}
