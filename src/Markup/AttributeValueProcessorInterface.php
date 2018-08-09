<?php

namespace Graphjs\Markup;

interface AttributeValueProcessorInterface
{
    /**
     * @param string $value
     * @return string
     * @throws \Graphjs\Markup\InvalidAttributeValueException
     */
    public function process($value);
}
