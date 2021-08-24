<?php

namespace Rinsvent\DTO2Data\Attribute;

#[\Attribute(\Attribute::TARGET_ALL|\Attribute::IS_REPEATABLE)]
class Schema
{
    public ?array $baseMap = null;

    public function __construct(
        public ?array $map = null,
        /** @var string[] $tags */
        public array $tags = ['default']
    ) {
        $this->map = $map ?? $this->baseMap;
    }
}
