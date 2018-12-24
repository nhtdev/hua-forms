<?php

namespace HuaForms\Registry;

class DefaultRegistrySet
{
    /**
     * @var RegistrySet
     */
    protected static $registrySet = null;
    
    public static function get() : RegistrySet
    {
        if (static::$registrySet === null) {
            static::$registrySet = new RegistrySet();
            
            static::$registrySet->registryRenderCreate
            ->register('label', \HuaForms\Registry\RenderCreate\Label::class)
            ->register('input', \HuaForms\Registry\RenderCreate\InputTypeText::class)
            ;
        }
        
        return static::$registrySet;
    }
    
}