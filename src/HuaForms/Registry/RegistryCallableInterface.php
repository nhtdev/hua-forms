<?php

namespace HuaForms\Registry;

/**
 * Interface for any class that may be called via a registry
 * @author x
 *
 */
interface RegistryCallableInterface
{
    /**
     * Executes the treatment for the given element
     * @param \HuaForms\Elements\Element|\HuaForms\Form $element
     */
    public function process($element) : void;
}