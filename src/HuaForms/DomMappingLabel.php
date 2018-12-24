<?php

namespace HuaForms;

trait DomMappingLabel
{
    protected $domLabel = null;
    
    public function getDomMappingLabel() 
    {
        return $this->domLabel;
    }
    
    public function hasDomMappingLabel() : bool
    {
        return $this->domLabel !== null;
    }
    
    public function setDomMappingLabel(\DOMNode $node)
    {
        $this->domLabel = $node;
        return $this;
    }
}