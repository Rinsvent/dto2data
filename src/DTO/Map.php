<?php

namespace Rinsvent\DTO2Data\DTO;

class Map
{
    public array $properties = [];
    public array $methods = [];

    public function addProperty(string $propertyName, ?Map $map = null): static
    {
        $this->properties[$propertyName] = $map;
        return $this;
    }

    public function addMethod(string $methodName, ?Map $map = null): static
    {
        $this->methods[$methodName] = $map;
        return $this;
    }
}