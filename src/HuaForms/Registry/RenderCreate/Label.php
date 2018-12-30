<?php

namespace HuaForms\Registry\RenderCreate;

/**
 * Create DOM element for a <label></label>
 * @author x
 *
 */
class Label implements \HuaForms\Registry\RegistryCallableInterface
{
    
    public function process($element) : void
    {
        $oldElement = $element->getDomMappingLabel();
        if ($oldElement === null) {
            // Todo Inline label
        } else {
            $oldElement->setAttribute('for', $element->getName());
        }
    }
    
}