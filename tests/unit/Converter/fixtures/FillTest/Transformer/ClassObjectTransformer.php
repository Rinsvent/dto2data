<?php

namespace Rinsvent\DTO2Data\Tests\unit\Converter\fixtures\FillTest\Transformer;

use Rinsvent\DTO2Data\Tests\unit\Converter\fixtures\FillTest\HelloClassTransformersRequest2;
use Rinsvent\Transformer\Transformer\Meta;
use Rinsvent\Transformer\Transformer\TransformerInterface;

class ClassObjectTransformer implements TransformerInterface
{
    /**
     * @param array|null $data
     * @param ClassData $meta
     */
    public function transform(mixed $data, Meta $meta): mixed
    {
        if ($data === null) {
            return $data;
        }
        $object = new HelloClassTransformersRequest2();
        $object->surname = '98789';
        $data = $object;
        return $data;
    }
}
