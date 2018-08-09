<?php

namespace Graphjs\Markup;

trait ElementNameWithPrefixTrait
{
    public function getName()
    {
        $prefix = 'graphjs-';
        return "{$prefix}{$this->name}";
    }
}
