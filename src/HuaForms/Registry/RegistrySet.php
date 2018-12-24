<?php

namespace HuaForms\Registry;

class RegistrySet
{
    /**
     * @var Registry
     */
    public $registryBuild;
    
    /**
     * @var Registry
     */
    public $registryRenderCreate;
    
    /**
     * @var Registry
     */
    public $registryRenderUpdate;

    public function __construct()
    {
        $this->registryBuild = new Registry();
        $this->registryRenderLabel = new Registry();
        $this->registryRenderCreate = new Registry();
        $this->registryRenderUpdate = new Registry();
    }
}
