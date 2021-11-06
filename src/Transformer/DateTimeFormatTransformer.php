<?php

namespace Rinsvent\DTO2Data\Transformer;

class DateTimeFormatTransformer implements TransformerInterface
{
    /**
     * @param \DateTimeImmutable|\DateTime|null $data
     * @param DateTimeFormat $meta
     */
    public function transform(&$data, Meta $meta): void
    {
        if ($data === null) {
            return;
        }
        $data = $data->format($meta->format);
    }
}