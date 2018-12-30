<?php

namespace HuaForms;

/**
 * Associate the label of a form element to a DOMNode
 * @author x
 *
 */
trait DomMappingLabel
{
    /**
     * Associated DOMNode
     * @var \DOMNode|null
     */
    protected $domLabel = null;
    
    /**
     * Returns the DOMNode associated to the label of the current element
     * @return \DOMNode|null
     */
    public function getDomMappingLabel() : ?\DOMNode
    {
        return $this->domLabel;
    }
    
    /**
     * Checks if the label of the current element is associated to a DOMNode
     * @return bool
     */
    public function hasDomMappingLabel() : bool
    {
        return $this->domLabel !== null;
    }
    
    /**
     * Associates the label of the current element to a DOMNode
     * @param \DOMNode $node
     */
    public function setDomMappingLabel(\DOMNode $node) : void
    {
        $this->domLabel = $node;
    }
}