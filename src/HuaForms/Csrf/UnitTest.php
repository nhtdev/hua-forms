<?php

namespace HuaForms\Csrf;

/**
 * CSRF storage engine : store in variables for PHPunit tests
 *
 */
class UnitTest implements CsrfInterface
{
    /**
     * Stored variables
     * @var array
     */
    protected $vars = [];
    
    /**
     * Constructor
     * @param array $params Not used
     */
    public function __construct(array $params)
    {
    }
    
    /**
     * Set the CSRF key and value
     * @param string $csrfKey CSRF key
     * @param string $csrfValue CSRF value
     */
    public function set(string $csrfKey, string $csrfValue) : void
    {
        $this->vars[$csrfKey] = $csrfValue;
    }
    
    /**
     * Check if the CSRF value has been saved
     * @param string $csrfKey CSRF key
     * @return bool
     */
    public function exists(string $csrfKey) : bool
    {
        return isset($this->vars[$csrfKey]);
    }
    
    /**
     * Return the CSRF value
     * @param string $csrfKey CSRF key
     * @return string|NULL
     */
    public function get(string $csrfKey) : ?string
    {
        return isset($this->vars[$csrfKey]) ? $this->vars[$csrfKey] : null;
    }
    
}