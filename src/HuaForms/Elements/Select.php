<?php

namespace HuaForms\Elements;

class Select extends Element
{
    protected $options = [];
    
    public function getMainType() : string
    {
        return 'select';
    }
    
    public function setOptions(array $options) : Select
    {
        $this->options = $options;
        return $this;
    }
    
    public function getOptions() : array
    {
        return $this->options;
    }
    
}
