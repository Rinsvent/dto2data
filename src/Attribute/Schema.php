<?php
declare(strict_types=1);

namespace Rinsvent\DTO2Data\Attribute;

#[\Attribute(\Attribute::TARGET_ALL|\Attribute::IS_REPEATABLE)]
class Schema
{
    public ?array $baseMap = null;

    public function __construct(
        public ?array $map = null,
    ) {
        $this->map = $map ?? $this->baseMap;
    }
}
