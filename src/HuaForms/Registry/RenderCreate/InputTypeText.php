<?php

namespace HuaForms\Registry\RenderCreate;

class InputTypeText implements \HuaForms\Registry\RegistryCallableInterface
{
    
    public function process($element)
    {
        if ($element->getAttribute('type') === 'text') {
            $oldElement = $element->getDomMapping();
            $newElement = $oldElement->ownerDocument->createElement('input');
            $newElement->setAttribute('type', 'text');
            $newElement->setAttribute('name', $element->getName());
            $newElement->setAttribute('id', $element->getName());
            $oldElement->parentNode->replaceChild($newElement, $oldElement);
            $element->getForm()->replaceDomMapping($oldElement, $newElement);
        }
    }
    
}