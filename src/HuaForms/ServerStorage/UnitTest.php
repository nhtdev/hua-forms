<?php

namespace HuaForms\ServerStorage;

/**
 * Storage engine for CSRF token & fronzen fields : store in variables for PHPunit tests
 *
 */
class UnitTest implements ServerStorageInterface
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
     * Set ta value
     * @param string $key key
     * @param string $value value
     */
    public function set(string $key, $value) : void
    {
        $this->vars[$key] = $value;
    }
    
    /**
     * Check if a value has been saved
     * @param string $key key
     * @return bool
     */
    public function exists(string $key) : bool
    {
        return isset($this->vars[$key]);
    }
    
    /**
     * Return a value
     * @param string $key key
     * @return mixed|NULL
     */
    public function get(string $key)
    {
        return $this->vars[$key] ?? null;
    }
    
}