<?php

namespace HuaForms\Elements;

use HuaForms\DomMappingLabel;
use HuaForms\ObjAttributes;
use HuaForms\Form;
use HuaForms\DomMapping;
use HuaForms\Entity;

/**
 * Abstract form element
 * @author x
 *
 */
abstract class Element implements Entity
{
    use ObjAttributes;
    use DomMapping;
    use DomMappingLabel;
    
    /**
     * Form to which the element is belonging to
     * @var Form
     */
    protected $form;
    
    /**
     * Element name
     * @var string
     */
    protected $name;
    
    /**
     * Group to which the element is belonging to (if any)
     * @var Group|null
     */
    protected $parent;
    
    /**
     * Constructor
     * @param Form $form Form to which the element is belonging to
     * @param string $name Element name
     * @param Element|null $parent Group to which the element is belonging to (if any)
     */
    public function __construct(Form $form, string $name, Element $parent=null)
    {
        $this->form = $form;
        $this->name = $name;
        $this->parent = $parent;
    }
    
    /**
     * Returns the form to which the element is belonging to
     * @return Form
     */
    public function getForm() : Form
    {
        return $this->form;
    }
    
    /**
     * Returns the name of the element
     * @return string
     */
    public function getName() : string
    {
        return $this->name;
    }
    
    /**
     * Sets the parent of the element
     * @param Element $parent
     */
    public function setParent(Element $parent) : void
    {
        $this->parent = $parent;
    }
    
    /**
     * Checks if the element has a parent element
     * @return bool
     */
    public function hasParent() : bool
    {
        return $this->parent !== null;
    }
    
    /**
     * Returns the element's parent (if any)
     * @return \HuaForms\Elements\Group|NULL
     */
    public function getParent() : ?Group
    {
        return $this->parent;
    }
    
    /**
     * Returns the attribute value, checking all ancestrors if the element does not have this attribute
     * @param string $attrName Attribute name
     * @return mixed|NULL Attribute value
     */
    public function getGlobalAttribute(string $attrName)
    {
        if ($this->hasAttribute($attrName)) {
            return $this->getAttribute($attrName);
        } else if ($this->hasParent()) {
            return $this->getParent()->getGlobalAttribute($attrName);
        } else {
            return $this->getForm()->getAttribute($attrName);
        }
    }
    
}
