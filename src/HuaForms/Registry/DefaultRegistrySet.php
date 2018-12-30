<?php

namespace HuaForms\Registry;

/**
 * This class creates and returns a complete Registry set for handling a HUA form
 * @author x
 *
 */
class DefaultRegistrySet
{
    /**
     * @var RegistrySet
     */
    protected static $registrySet = null;
    
    /**
     * Returns a complete Registry set for handling a HUA form
     * @return RegistrySet
     */
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