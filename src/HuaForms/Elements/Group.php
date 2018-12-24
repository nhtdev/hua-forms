<?php

namespace HuaForms\Elements;

class Group extends Element
{
    protected $elements = [];
    
    protected $repeatedMin = 1;
    protected $repeatedMax = 1;
    
    public function getMainType() : string
    {
        return 'group';
    }
    
    public function setRepeated($min, $max) : Group
    {
        $this->repeatedMin = $min;
        $this->repeatedMax = $max;
        return $this;
    }
    
    public function getRepeated() : array
    {
        return ['min' => $this->repeatedMin, 'max' => $this->repeatedMax];
    }
    
    public function addElement(Element $el) : Group
    {
        $el->setParent($this);
        $this->elements[] = $el;
        return $this;
    }
    
    public function getElements() : array
    {
        return $this->elements;
    }
    
    public function mapElements(Callable $callback)
    {
        foreach ($this->elements as $element) {
            $callback($element);
            if ($element instanceof \HuaForms\Elements\Group) {
                $element->mapElements($callback);
            }
        }
    }
    
}
