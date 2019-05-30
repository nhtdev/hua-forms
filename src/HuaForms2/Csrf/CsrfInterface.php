<?php

namespace HuaForms2\Csrf;

/**
 * Interface for CSRF storage engine
 *
 */
interface CsrfInterface
{

    /**
     * Constructor
     * @param array $params
     */
    public function __construct(array $params);
    
    /**
     * Set the CSRF key and value
     * @param string $csrfKey CSRF key
     * @param string $csrfValue CSRF value
     */
    public function set(string $csrfKey, string $csrfValue);
    
    /**
     * Check if the CSRF value has been saved
     * @param string $csrfKey CSRF key
     * @return bool
     */
    public function exists(string $csrfKey) : bool;
    
    /**
     * Return the CSRF value
     * @param string $csrfKey CSRF key
     * @return string|NULL
     */
    public function get(string $csrfKey) : ?string;
    
}