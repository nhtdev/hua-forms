<?php

namespace HuaForms\Elements;

use HuaForms\Form;

/**
 * Factory for building form elements
 * @author x
 *
 */
class ElementFactory
{
    
    /**
     * List of elements class + attributes by type
     * @var array
     */
    protected static $map = [
        'input'     => [\HuaForms\Elements\Input::class, ['type' => 'text']],
        'text'      => [\HuaForms\Elements\Input::class, ['type' => 'text']],
        'textarea'  => [\HuaForms\Elements\Input::class, ['type' => 'textarea']],
        'select'    => [\HuaForms\Elements\Select::class, ['type' => 'select']],
    ];
   
    /**
     * Registers a new element type
     * @param string $type Element type
     * @param string $class Element class
     * @param array $attributes Element attributes
     */
    public static function register(string $type, string $class, array $attributes=[]) : void
    {
       static::$map[$type] = [$class, $attribute];
    }
    
    /**
     * Checks if an element type is registred
     * @param string $type Element type
     * @return bool
     */
    public static function isRegistered(string $type) : bool
    {
       return isset(static::$map[$type]);
    }
    
    /**
     * Creates a new element
     * @param Form $form Element form 
     * @param string $type Element type
     * @param string $name Element name
     * @throws UnexpectedValueException
     * @return Element
     */
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