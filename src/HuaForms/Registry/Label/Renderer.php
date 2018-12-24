<?php

namespace HuaForms\Registry\Label;

class Renderer implements \HuaForms\Registry\Renderer
{
    
    public function render($element)
    {
        $oldElement = $element->getDomMappingLabel();
        if ($oldElement === null) {
            // Todo Inline label
        } else {
            $oldElement->setAttribute('for', $element->getName());
        }
    }
    
}