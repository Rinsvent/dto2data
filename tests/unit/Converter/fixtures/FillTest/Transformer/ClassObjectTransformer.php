<?php

namespace Rinsvent\DTO2Data\Tests\unit\Converter\fixtures\FillTest\Transformer;

use Rinsvent\DTO2Data\Tests\unit\Converter\fixtures\FillTest\HelloClassTransformersRequest2;
use Rinsvent\DTO2Data\Transformer\Meta;
use Rinsvent\DTO2Data\Transformer\TransformerInterface;

class ClassObjectTransformer implements TransformerInterface
{
    /**
     * @param array|null $data
     * @param ClassData $meta
     */
    public function transform(&$data, Meta $meta): void
    {
        if ($data === null) {
            return;
        }
        $object = new HelloClassTransformersRequest2();
        $object->surname = '98789';
        $data = $object;
    }
}
