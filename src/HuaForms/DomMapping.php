<?php

namespace HuaForms;

trait DomMapping
{
    protected $dom = null;
    
    public function getDomMapping() 
    {
        return $this->dom;
    }
    
    public function hasDomMapping() : bool
    {
        return $this->dom !== null;
    }
    
    public function setDomMapping(\DOMNode $node)
    {
        $this->dom = $node;
        return $this;
    }
}