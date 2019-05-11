<?php

namespace HuaForms;

/**
 * Entity : can be any form element or the form itself
 * @author x
 *
 */
interface Entity
{
    
    /**
     * Returns the type of the element
     * @return string
     */
    public function getMainType() : string;
    
    /**
     * Returns the attribute value, checking all ancestrors if the element does not have this attribute
     * @param string $attrName Attribute name
     * @return mixed|NULL Attribute value
     */
    public function getGlobalAttribute(string $attrName);
    
}