<?php

namespace HuaForms\Elements;

/**
 * Element : group of other elements
 * @author x
 *
 */
class Group extends Element
{
    /**
     * Elements of this group (array of Element)
     * @var array
     */
    protected $elements = [];
    
    /**
     * Minimum number of group repetition
     * @var integer
     */
    protected $repeatedMin = 1;
    
    /**
     * Maximum number of group repetition
     * @var integer
     */
    protected $repeatedMax = 1;
    
    /**
     * Returns the type of the element : "group"
     * @return string
     */
    public function getMainType() : string
    {
        return 'group';
    }
    
    /**
     * Sets a repetition for this group
     * @param integer $min Minimum number of repetitions
     * @param integer $max Maximum number of repetitions
     * @return Group
     */
    public function setRepeated(int $min, int $max) : void
    {
        $this->repeatedMin = $min;
        $this->repeatedMax = $max;
    }
    
    /**
     * Returns the number of repetition : 
     * [ 'min' => Minimum number of repetitions, 'max' => Maximum number of repetitions ]
     * @return array
     */
    public function getRepeated() : array
    {
        return ['min' => $this->repeatedMin, 'max' => $this->repeatedMax];
    }
    
    /**
     * Adds an element to the group
     * @param Element $el
     */
    public function addElement(Element $el) : void
    {
        $el->setParent($this);
        $this->elements[] = $el;
    }
    
    /**
     * Returns all the elements of the group
     * @return array Array of Element
     */
    public function getElements() : array
    {
        return $this->elements;
    }
    
    /**
     * Applies a function to all the elements of the group
     * (including the elements inside another group of this group)
     * @param callable $callback
     */
    public function mapElements(Callable $callback) : void
    {
        foreach ($this->elements as $element) {
            $callback($element);
            if ($element instanceof \HuaForms\Elements\Group) {
                $element->mapElements($callback);
            }
        }
    }
    
}
