<?php

namespace HuaForms\Registry\RenderCreate;

/**
 * Create DOM element for an <input type="text" />
 * @author x
 *
 */
class InputTypeText implements \HuaForms\Registry\RegistryCallableInterface
{
    /**
     * {@inheritDoc}
     * @see \HuaForms\Registry\RegistryCallableInterface::process()
     */
    public function process(\HuaForms\Entity $element) : void
    {
        if ($element->getAttribute('type') === 'text') {
            $domElement = $element->getDomMapping();
            $domElement->setAttribute('type', 'text');
            /*
            $oldElement = $element->getDomMapping();
            $newElement = $oldElement->ownerDocument->createElement('input');
            $newElement->setAttribute('type', 'text');
            $newElement->setAttribute('name', $element->getName());
            $newElement->setAttribute('id', $element->getName());
            $oldElement->parentNode->replaceChild($newElement, $oldElement);
            $element->getForm()->replaceDomMapping($oldElement, $newElement);
            */
        }
    }
    
}