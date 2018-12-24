<?php

namespace HuaForms\Elements;

use HuaForms\DomMappingLabel;
use HuaForms\ObjAttributes;
use HuaForms\Form;
use HuaForms\DomMapping;

abstract class Element
{
    use ObjAttributes;
    use DomMapping;
    use DomMappingLabel;
    
    protected $form;
    protected $name;
    protected $parent;
    
    public function __construct(Form $form, string $name, Element $parent=null)
    {
        $this->form = $form;
        $this->name = $name;
        $this->parent = $parent;
    }
    
    abstract public function getMainType() : string;
    
    public function getForm() : Form
    {
        return $this->form;
    }
    
    public function getName() : string
    {
        return $this->name;
    }
    
    public function setParent(Element $parent) : Element
    {
        $this->parent = $parent;
        return $this;
    }
    
    public function hasParent() : bool
    {
        return $this->parent !== null;
    }
    
    public function getParent()
    {
        return $this->parent;
    }
    
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
