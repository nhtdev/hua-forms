<?php

namespace HuaForms\Registry\RenderUpdate;

/**
 * Format element for using it in bootstrap
 * @author x
 *
 */
class FormatBootstrap implements \HuaForms\Registry\RegistryCallableInterface
{
    public function process(\HuaForms\Entity $element) : void
    {
        $type = $element->getMainType();
        //var_dump($type); exit;
    }
    
}