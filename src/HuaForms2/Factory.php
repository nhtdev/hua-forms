<?php

namespace HuaForms2;

class Factory
{
    protected static $options = [];
    protected static $forms = [];
    
    public static function form(string $formName) : Facade
    {
        if (!isset(static::$forms[$formName])) {
            static::$forms[$formName] = new Facade($formName, static::$options);
        }
        return static::$forms[$formName];
    }
    
    public static function setOptions(array $options) : void
    {
        static::$options = $options;
    }
    
}