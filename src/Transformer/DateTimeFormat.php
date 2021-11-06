<?php

namespace Rinsvent\DTO2Data\Transformer;

#[\Attribute(\Attribute::TARGET_ALL|\Attribute::IS_REPEATABLE)]
class DateTimeFormat extends Meta
{
    public function __construct(
        public array $tags = ['default'],
        public string $format = \DateTimeInterface::ATOM
    ) {
        parent::__construct(...func_get_args());
    }
}