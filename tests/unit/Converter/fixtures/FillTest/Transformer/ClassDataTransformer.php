<?php

namespace Rinsvent\DTO2Data\Tests\unit\Converter\fixtures\FillTest\Transformer;

use Rinsvent\DTO2Data\Transformer\Meta;
use Rinsvent\DTO2Data\Transformer\TransformerInterface;

class ClassDataTransformer implements TransformerInterface
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

        if (isset($data['surname'])) {
            $data['surname'] = '123454321';
        }
    }
}
