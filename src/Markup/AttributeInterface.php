<?php

namespace Graphjs\Markup;

interface AttributeInterface
{
    /**
     * @return string Attribute name
     */
    public function getName();

    /**
     * @return \Graphjs\Markup\AttributeValueProcessorInterface
     */
    public function getValueProcessor();
}
