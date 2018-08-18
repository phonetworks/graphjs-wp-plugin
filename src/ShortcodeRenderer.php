<?php

namespace Graphjs;

class ShortcodeRenderer
{
    private $element;

    public function __construct($element)
    {
        $this->element = $element;
    }

    function render($atts = [], $content = null, $tag = '')
    {
        // normalize attribute keys, lowercase
        $atts = array_change_key_case((array) $atts, CASE_LOWER);

        $dom = new \DOMDocument('1.0');
        $domElement = $dom->createElement($this->element);

        foreach ($atts as $attributeName => $attributeValue) {
            try {
                $domAttribute = $dom->createAttribute($attributeName);
            }
            catch (\DOMException $ex) {
                continue;
            }
            $domAttribute->value = $attributeValue;
            $domElement->appendChild($domAttribute);
        }

        $dom->appendChild($domElement);
        $output = $dom->saveHTML();
        return $output;
    }
}
