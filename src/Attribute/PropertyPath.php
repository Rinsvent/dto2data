<?php

namespace Rinsvent\DTO2Data\Attribute;

#[\Attribute(\Attribute::TARGET_ALL|\Attribute::IS_REPEATABLE)]
class PropertyPath
{
    public function __construct(
        public string $path,
        /** @var string[] $tags */
        public array $tags = ['default']
    ) {}
}