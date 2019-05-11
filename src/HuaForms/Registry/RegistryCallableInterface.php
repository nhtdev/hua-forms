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
     * @param \HuaForms\Entity $element
     */
    public function process(\HuaForms\Entity $element) : void;
}