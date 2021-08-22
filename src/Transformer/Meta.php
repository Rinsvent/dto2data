<?php

namespace Rinsvent\DTO2Data\Transformer;

#[\Attribute(\Attribute::TARGET_ALL|\Attribute::IS_REPEATABLE)]
abstract class Meta
{
    public const TYPE = 'simple';
    public ?string $returnType = null;
    public ?bool $allowsNull = null;

    public function __construct(
        public array $tags = ['default']
    ) {}
}