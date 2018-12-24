<?php

namespace HuaForms;

trait ObjAttributes
{
    
    protected $attributes = [];
    
    public function setAttribute(string $attrName, $attrValue)
    {
        if ($attrValue === null) {
            if (isset($this->attributes[$attrName])) {
                unset($this->attributes[$attrName]);
            }
        } else {
            $this->attributes[$attrName] = $attrValue;
        }
        return $this;
    }
    
    public function getAttribute(string $attrName)
    {
        if (isset($this->attributes[$attrName])) {
            return $this->attributes[$attrName];
        } else {
            return null;
        }
    }
    
    public function hasAttribute(string $attrName) : bool
    {
        return isset($this->attributes[$attrName]);
    }
    
    public function removeAttribute(string $attrName)
    {
        $this->setAttribute($attrName, null);
        return $this;
    }
    
    public function setAttributes(array $attributes)
    {
        $this->attributes = array_merge($this->attributes, $attributes);
        return $this;
    }
    
    public function getAttributes() : array
    {
        return $this->attributes;
    }
    
    public function removeAllAttributes()
    {
        $this->attributes = [];
        return $this;
    }
    
}