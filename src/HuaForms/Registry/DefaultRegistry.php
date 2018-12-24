<?php

namespace HuaForms\Registry;

use HuaForms\Registry\Registry;

class DefaultRegistry
{
    protected static $registry = null;
    
    public static function get() : Registry
    {
        if (static::$registry === null) {
            static::$registry = new Registry();
            static::$registry
            ->register('input', 'type', 'text', null, \HuaForms\Registry\Input\Type\Text\Renderer::class)
            //->register('input', 'type', 'textarea', null, \HuaForms\Registry\Input\Type\Textarea\Renderer::class)
            //->register('select', 'type', 'select', \HuaForms\Registry\Select\Type\Select\Builder::class, \HuaForms\Registry\Select\Type\Select\Renderer::class)
            ->register('label', '*', '*', null, \HuaForms\Registry\Label\Renderer::class)
            ;
        }
        
        return static::$registry;
    }
    
}