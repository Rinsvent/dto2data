<?php
declare(strict_types=1);

namespace Rinsvent\DTO2Data\Attribute;

#[\Attribute(\Attribute::TARGET_ALL|\Attribute::IS_REPEATABLE)]
class Schema
{
    protected array $baseMap = [];

    public function __construct(
        public array $map = [],
    ) {
    }

    public function getMap(): array
    {
        return $this->baseMap ?: $this->map;
    }

    public function getTags(mixed $data = null): array
    {
        return ['default'];
    }
}
