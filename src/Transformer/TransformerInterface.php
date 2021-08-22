<?php

namespace Rinsvent\DTO2Data\Transformer;

interface TransformerInterface
{
    public function transform(&$data, Meta $meta): void;
}