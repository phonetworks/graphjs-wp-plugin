<?php

namespace Graphjs\Markup\AttributeProcessor;

use Graphjs\Markup\AttributeValueProcessorInterface;
use Graphjs\Markup\InvalidAttributeValueException;

class PredefinedValuesProcessor implements AttributeValueProcessorInterface
{
    private $predefinedValues;

    /**
     * @param string[] $predefinedValues
     */
    public function __construct(array $predefinedValues)
    {
        $this->predefinedValues = $predefinedValues;
    }

    public function process($value)
    {
        if (! in_array($value, $this->predefinedValues)) {
            throw new InvalidAttributeValueException();
        }

        return $value;
    }
}
