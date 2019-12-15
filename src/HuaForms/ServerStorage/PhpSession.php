<?php

namespace HuaForms\ServerStorage;

/**
 * Storage engine for CSRF token & frozen fields : store in PHP sessions
 *
 */
class PhpSession implements ServerStorageInterface
{
    
    /**
     * Constructor
     * @param array $params Not used
     */
    public function __construct(array $params)
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    }
    
    /**
     * Set a value
     * @param string $key key
     * @param mixed $value value
     */
    public function set(string $key, $value) : void
    {
        $_SESSION['__forms_'.$key] = $value;
    }
    
    /**
     * Check if a value has been saved
     * @param string $key key
     * @return bool
     */
    public function exists(string $key) : bool
    {
        return isset($_SESSION['__forms_'.$key]);
    }
    
    /**
     * Return a value
     * @param string $key key
     * @return mixed|NULL
     */
    public function get(string $key)
    {
        return $_SESSION['__forms_'.$key] ?? null;
    }
    
}