<?php

namespace HuaForms\Registry\RenderUpdate;

/**
 * Removes the "label" attribute, which does not exists in html
 * @author x
 *
 */
class RemoveLabelAttribute implements \HuaForms\Registry\RegistryCallableInterface
{
    public function process(\HuaForms\Entity $element) : void
    {
        $domElement = $element->getDomMapping();
        if ($domElement->hasAttribute('label')) {
            $domElement->removeAttribute('label');
        }
    }
    
}