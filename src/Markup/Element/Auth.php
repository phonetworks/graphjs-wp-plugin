<?php

namespace Graphjs\Markup\Element;

use Graphjs\Markup\AttributeProcessor\PredefinedValuesProcessor;
use Graphjs\Markup\BasicAttribute;
use Graphjs\Markup\ElementInterface;
use Graphjs\Markup\ElementNameWithPrefixTrait;

class Auth implements ElementInterface
{
    use ElementNameWithPrefixTrait;

    private $name = 'auth';

    private $attributes = [];

    public function __construct()
    {
        $this->_initAttributes();
    }

    private function _initAttributes()
    {
        $attributes = [];

        $attributes[] = new BasicAttribute('position', new PredefinedValuesProcessor([
            'topleft',
            'topright',
            'bottomleft',
            'bottomright',
        ]));

        foreach ($attributes as $attribute) {
            $this->attributes[$attribute->getName()] = $attribute;
        }
    }

    public function getAttributes()
    {
        return $this->attributes;
    }
}
