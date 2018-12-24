<?php

namespace HuaForms\Registry\Input\Type\Text;

class Renderer implements \HuaForms\Registry\Renderer
{
    
    public function render($element)
    {
        $oldElement = $element->getDomMapping();
        $newElement = $oldElement->ownerDocument->createElement('input');
        $newElement->setAttribute('type', 'text');
        $newElement->setAttribute('name', $element->getName());
        $newElement->setAttribute('id', $element->getName());
        $oldElement->parentNode->replaceChild($newElement, $oldElement);
        $element->getForm()->replaceDomMapping($oldElement, $newElement);
    }
    
}