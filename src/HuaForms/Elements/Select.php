<?php

namespace HuaForms\Elements;

/**
 * Input element, where the value(s) of the element is restricted to a list of values
 * @author x
 *
 */
class Select extends Element
{
    /**
     * Possible values : ['value' => 'Label']
     * @var array
     */
    protected $options = [];
    
    /**
     * Returns the type of the element : "select"
     * @return string
     */
    public function getMainType() : string
    {
        return 'select';
    }
    
    /**
     * Sets the possible values of the elements
     * @param array $options ['value' => 'Label']
     */
    public function setOptions(array $options) : void
    {
        $this->options = $options;
    }
    
    /**
     * Returns the possible values of the elements
     * @return array ['value' => 'Label']
     */
    public function getOptions() : array
    {
        return $this->options;
    }
    
}
