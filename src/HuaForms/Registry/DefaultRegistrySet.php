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
            
            static::$registrySet->registryBuild
            ->register('form', \HuaForms\Registry\Build\FormDefaultAttributes::class)
            ;
            
            static::$registrySet->registryRenderCreate
            ->register('label', \HuaForms\Registry\RenderCreate\Label::class)
            ->register('input', \HuaForms\Registry\RenderCreate\InputTypeText::class)
            ;
            
            static::$registrySet->registryRenderUpdate
            ->register('input', \HuaForms\Registry\RenderUpdate\RemoveLabelAttribute::class)
            ->register('select', \HuaForms\Registry\RenderUpdate\RemoveLabelAttribute::class)
            ->register('input', \HuaForms\Registry\RenderUpdate\FormatBootstrap::class)
            ->register('select', \HuaForms\Registry\RenderUpdate\FormatBootstrap::class)
            ;
        }
        
        return static::$registrySet;
    }
    
}