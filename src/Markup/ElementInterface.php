<?php

namespace Graphjs\Markup;

interface ElementInterface
{
    /**
     * @return string Element name
     */
    public function getName();

    /**
     * @return array Attributes supported by this element
     */
    public function getAttributes();
}
