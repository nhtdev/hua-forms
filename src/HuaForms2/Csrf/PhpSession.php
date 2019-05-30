<?php

namespace HuaForms2\Csrf;

/**
 * CSRF storage engine : store in PHP sessions
 *
 */
class PhpSession implements CsrfInterface
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
     * Set the CSRF key and value
     * @param string $csrfKey CSRF key
     * @param string $csrfValue CSRF value
     */
    public function set(string $csrfKey, string $csrfValue) : void
    {
        $_SESSION['__csrf_'.$csrfKey] = $csrfValue;
    }
    
    /**
     * Check if the CSRF value has been saved
     * @param string $csrfKey CSRF key
     * @return bool
     */
    public function exists(string $csrfKey) : bool
    {
        return isset($_SESSION['__csrf_'.$csrfKey]);
    }
    
    /**
     * Return the CSRF value
     * @param string $csrfKey CSRF key
     * @return string|NULL
     */
    public function get(string $csrfKey) : ?string
    {
        return isset($_SESSION['__csrf_'.$csrfKey]) ? $_SESSION['__csrf_'.$csrfKey] : null;
    }
    
}