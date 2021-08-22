<?php

namespace Rinsvent\DTO2Data\Resolver;

use Rinsvent\DTO2Data\Transformer\Meta;
use Rinsvent\DTO2Data\Transformer\TransformerInterface;

class SimpleResolver implements TransformerResolverInterface
{
    public function resolve(Meta $meta): TransformerInterface
    {
        $metaClass = $meta::class;
        $transformerClass = $metaClass . 'Transformer';
        return new $transformerClass;
    }
}