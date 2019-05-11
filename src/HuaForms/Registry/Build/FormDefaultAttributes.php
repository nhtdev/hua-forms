<?php

namespace HuaForms\Registry\Build;

/**
 * Adds the default attributes for a form element
 * @author x
 *
 */
class FormDefaultAttributes implements \HuaForms\Registry\RegistryCallableInterface
{
    public function process(\HuaForms\Entity $element) : void
    {
        $element->setAttribute('format-bootstrap', true);
    }
    
}