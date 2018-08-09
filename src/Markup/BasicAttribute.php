<?php

namespace Graphjs\Markup;

class BasicAttribute implements AttributeInterface
{
    private $name;

    private $valueProcessor;

    public function __construct($name, AttributeValueProcessorInterface $valueProcessor)
    {
        $this->name = $name;
        $this->valueProcessor = $valueProcessor;
    }

    public function getName()
    {
        return $this->name;
    }

    public function getValueProcessor()
    {
        return $this->valueProcessor;
    }
}
