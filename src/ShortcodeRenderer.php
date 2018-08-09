<?php

namespace Graphjs;

use Graphjs\Markup\ElementInterface;
use Graphjs\Markup\InvalidAttributeValueException;

class ShortcodeRenderer
{
    private $element;

    public function __construct(ElementInterface $element)
    {
        $this->element = $element;
    }

    function render($atts = [], $content = null, $tag = '')
    {
        // normalize attribute keys, lowercase
        $atts = array_change_key_case((array) $atts, CASE_LOWER);

        $supportedAttributes = $this->element->getAttributes();
        $shortcodeAttributes = array_fill_keys(array_keys($supportedAttributes), null);

        $graphjsAtts = shortcode_atts($shortcodeAttributes, $atts, $tag);

        $dom = new \DOMDocument('1.0');
        $domElement = $dom->createElement('graphjs-auth');

        foreach ($supportedAttributes as $attributeName => $supportedAttribute) {
            if (isset($graphjsAtts[$attributeName])) {
                try {
                    $attributeValue = $supportedAttribute->getValueProcessor()
                        ->process($graphjsAtts[$attributeName]);
                }
                catch (InvalidAttributeValueException $ex) {
                    continue;
                }
                $domAttribute = $dom->createAttribute($attributeName);
                $domAttribute->value = $attributeValue;
                $domElement->appendChild($domAttribute);
            }
        }

        $dom->appendChild($domElement);
        $output = $dom->saveHTML();
        return $output;
    }
}
