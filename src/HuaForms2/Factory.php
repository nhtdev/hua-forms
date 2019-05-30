<?php

namespace HuaForms2;

/**
 * Form factory : easy generation of form facades
 *
 */
class Factory
{
    /**
     * Forms options
     * @var array
     */
    protected static $options = [];
    
    /**
     * Generated forms facades
     * @var array
     */
    protected static $forms = [];
    
    /**
     * Set forms options
     * @param array $options Forms options
     */
    public static function setOptions(array $options) : void
    {
        static::$options = $options;
    }
    
    /**
     * Generate and return a form facade object
     * @param string $formName Form name
     * @return Facade
     */
    public static function form(string $formName) : Facade
    {
        if (!isset(static::$forms[$formName])) {
            static::$forms[$formName] = new Facade($formName, static::$options);
        }
        return static::$forms[$formName];
    }
    
}