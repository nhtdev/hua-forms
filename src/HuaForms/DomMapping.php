<?php

namespace HuaForms;

/**
 * Associate a form element to a DOMNode
 * @author x
 *
 */
trait DomMapping
{
    /**
     * Associated DOMNode
     * @var \DOMNode|null
     */
    protected $dom = null;
    
    /**
     * Returns the DOMNode associated to the current element
     * @return \DOMNode|null
     */
    public function getDomMapping() : ?\DOMNode
    {
        return $this->dom;
    }
    
    /**
     * Checks if the current element is associated to a DOMNode
     * @return bool
     */
    public function hasDomMapping() : bool
    {
        return $this->dom !== null;
    }
    
    /**
     * Associates the current element to a DOMNode
     * @param \DOMNode $node
     */
    public function setDomMapping(\DOMNode $node) : void
    {
        $this->dom = $node;
    }
}