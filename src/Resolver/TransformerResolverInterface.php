<?php

namespace Rinsvent\DTO2Data\Resolver;

use Rinsvent\DTO2Data\Transformer\Meta;
use Rinsvent\DTO2Data\Transformer\TransformerInterface;

interface TransformerResolverInterface
{
    public function resolve(Meta $meta): TransformerInterface;
}