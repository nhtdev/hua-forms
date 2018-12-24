<?php

namespace HuaForms\Elements;

use HuaForms\Form;

class ElementFactory
{
    
    protected static $map = [
        'input'     => [\HuaForms\Elements\Input::class, ['type' => 'text']],
        'text'      => [\HuaForms\Elements\Input::class, ['type' => 'text']],
        'textarea'  => [\HuaForms\Elements\Input::class, ['type' => 'textarea']],
        'select'    => [\HuaForms\Elements\Select::class, ['type' => 'select']],
    ];
    
   public static function register(string $type, string $class, array $attributes=[])
   {
       static::$map[$type] = [$class, $attribute];
   }
   
   public static function isRegistered(string $type) : bool
   {
       return isset(static::$map[$type]);
   }
   
   public static function create(Form $form, string $type, string $name) : Element
   {
       if (!static::isRegistered($type)) {
           throw new UnexpectedValueException('HuaForms : invalid element type "'.$type.'"');
       } else {
           $class = static::$map[$type][0];
           $attributes = static::$map[$type][1];
           $el = new $class($form, $name);
           $el->setAttributes($attributes);
           return $el;
       }
   }
   
}