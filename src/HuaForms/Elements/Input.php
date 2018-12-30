<?php

namespace HuaForms\Elements;

/**
 * Input element
 * @author x
 *
 */
class Input extends Element
{
    /**
     * Returns the type of the element : "input"
     * @return string
     */
    public function getMainType() : string
    {
        return 'input';
    }
}
