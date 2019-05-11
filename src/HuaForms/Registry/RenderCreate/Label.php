<?php

namespace HuaForms\Registry\RenderCreate;

/**
 * Create DOM element for a <label></label>
 * @author x
 *
 */
class Label implements \HuaForms\Registry\RegistryCallableInterface
{
    
    public function process(\HuaForms\Entity $element) : void
    {
        $domLabel = $element->getDomMappingLabel();
        if ($domLabel === null) {
            $domInput = $element->getDomMapping();
            
            $domLabel = $domInput->ownerDocument->createElement('label');
            $domLabel->nodeValue = $element->getAttribute('label');
            $domLabel->setAttribute('for', $element->getName());
            $domLabel = $domInput->parentNode->insertBefore($domLabel, $domInput);
            
            $domNewLine = $domInput->ownerDocument->createTextNode("\n");
            $domInput->parentNode->insertBefore($domNewLine, $domInput);
            
            $element->setDomMappingLabel($domLabel);
        } else {
            $domLabel->setAttribute('for', $element->getName());
        }
    }
    
}