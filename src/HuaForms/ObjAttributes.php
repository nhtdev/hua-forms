<?php

namespace HuaForms;

/**
 * Manage attributes on any object (form, element, ...)
 * Attributes are a list of 'name' => 'value'
 * @author x
 *
 */
trait ObjAttributes
{
    /**
     * Attributes : [ 'name' => 'value' ]
     * @var array
     */
    protected $attributes = [];
    
    /**
     * Defines an attribute
     * @param string $attrName Attribute name
     * @param mixed $attrValue Attribute value, or null to unset the attribute 
     */
    public function setAttribute(string $attrName, $attrValue) : void
    {
        if ($attrValue === null) {
            if (isset($this->attributes[$attrName])) {
                unset($this->attributes[$attrName]);
            }
        } else {
            $this->attributes[$attrName] = $attrValue;
        }
    }
    
    /**
     * Returns the attribute value (or null if not defined)
     * @param string $attrName Attribute name
     * @return mixed|NULL Attribute value
     */
    public function getAttribute(string $attrName)
    {
        if (isset($this->attributes[$attrName])) {
            return $this->attributes[$attrName];
        } else {
            return null;
        }
    }
    
    /**
     * Checks if the specified attribute has been defined
     * @param string $attrName Attribute name
     * @return bool
     */
    public function hasAttribute(string $attrName) : bool
    {
        return isset($this->attributes[$attrName]);
    }
    
    /**
     * Remove an attribute
     * @param string $attrName Attribute name
     */
    public function removeAttribute(string $attrName) : void
    {
        $this->setAttribute($attrName, null);
    }
    
    /**
     * Defines many attributes
     * @param array $attributes Array of attributes : [ 'name' => 'Value' ]
     */
    public function setAttributes(array $attributes) : void
    {
        $this->attributes = array_merge($this->attributes, $attributes);
    }
    
    /**
     * Returns all attributes
     * @return array Array of attributes : [ 'name' => 'Value' ]
     */
    public function getAttributes() : array
    {
        return $this->attributes;
    }
    
    /**
     * Remove all attributes
     */
    public function removeAllAttributes() : void
    {
        $this->attributes = [];
    }
    
}