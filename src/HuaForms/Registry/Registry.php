<?php

namespace HuaForms\Registry;

class Registry
{
    
    protected $registry = [];
    
    public function register(string $elementType, string $callClass) : Registry
    {
        $this->registry[$elementType][] = $callClass;
        return $this;
    }
    
    public function get(string $elementType) : array
    {
        if (isset($this->registry[$elementType])) {
            return $this->registry[$elementType];
        } else {
            return [];
        }
    }
    
}