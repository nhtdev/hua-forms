<?php

namespace HuaForms\Registry;

/**
 * List of all registries available for a form
 * @author x
 *
 */
class RegistrySet
{
    /**
     * Building registry (called after parsing)
     * @var Registry
     */
    public $registryBuild;
    
    /**
     * Render "create" registry : called first before rendering, for creating DOM elements
     * @var Registry
     */
    public $registryRenderCreate;
    
    /**
     * Render "update" registry : called second before rendering, for updating existing DOM elements
     * @var Registry
     */
    public $registryRenderUpdate;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->registryBuild = new Registry();
        $this->registryRenderLabel = new Registry();
        $this->registryRenderCreate = new Registry();
        $this->registryRenderUpdate = new Registry();
    }
}
