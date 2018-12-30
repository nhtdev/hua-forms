<?php

namespace HuaForms\Registry;

/**
 * List of class to be called on some context
 * @author x
 *
 */
class Registry
{
    /**
     * Registred classes
     * @var array
     */
    protected $registry = [];
    
    /**
     * Register a class to be called for an element type
     * @param string $elementType Element type
     * @param string $callClass Class name
     * @return Registry $this for fluent interface
     */
    public function register(string $elementType, string $callClass) : Registry
    {
        $this->registry[$elementType][] = $callClass;
        return $this;
    }
    
    /**
     * Returns the registred classes for the given element type
     * @param string $elementType Element type
     * @return array[string]
     */
    public function get(string $elementType) : array
    {
        if (isset($this->registry[$elementType])) {
            return $this->registry[$elementType];
        } else {
            return [];
        }
    }
    
}