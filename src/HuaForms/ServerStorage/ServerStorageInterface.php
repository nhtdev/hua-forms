<?php

namespace HuaForms\ServerStorage;

/**
 * Interface for storage engine (CSRF token, frozen values, ...)
 *
 */
interface ServerStorageInterface
{

    /**
     * Constructor
     * @param array $params
     */
    public function __construct(array $params);
    
    /**
     * Set a value
     * @param string $key key
     * @param mixed $value value
     */
    public function set(string $key, $value);
    
    /**
     * Check if a value has been saved
     * @param string $key key
     * @return bool
     */
    public function exists(string $key) : bool;
    
    /**
     * Return a value
     * @param string $key key
     * @return mixed|NULL
     */
    public function get(string $key);
    
}