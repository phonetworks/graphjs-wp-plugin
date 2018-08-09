<?php

namespace Graphjs;

use Graphjs\Markup\Element\Auth;

class Graphjs
{
    private $elements = [];

    public function __construct()
    {
        $this->_initElements();
    }

    private function _initElements()
    {
        $elements = [];

        $elements[] = new Auth();

        foreach ($elements as $element) {
            $this->elements[$element->getName()] = $element;
        }
    }

    /**
     * @return array
     */
    public function getElements()
    {
        return $this->elements;
    }
}
