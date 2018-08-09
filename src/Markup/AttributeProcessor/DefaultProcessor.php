<?php

namespace Graphjs\Markup\AttributeProcessor;

use Graphjs\Markup\AttributeValueProcessorInterface;

class DefaultProcessor implements AttributeValueProcessorInterface
{
    public function process($value)
    {
        return $value;
    }
}
