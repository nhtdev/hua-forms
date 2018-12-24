<?php

namespace HuaForms\Registry\RenderCreate;

class Label implements \HuaForms\Registry\RegistryCallableInterface
{
    
    public function process($element)
    {
        $oldElement = $element->getDomMappingLabel();
        if ($oldElement === null) {
            // Todo Inline label
        } else {
            $oldElement->setAttribute('for', $element->getName());
        }
    }
    
}