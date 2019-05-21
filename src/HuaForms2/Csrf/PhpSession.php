<?php

namespace HuaForms2\Csrf;

use HuaForms2\Csrf\CsrfInterface;

class PhpSession implements CsrfInterface
{
    
    public function __construct(array $params)
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    }
    
    public function set(string $csrfKey, string $csrfValue) : void
    {
        $_SESSION['__csrf_'.$csrfKey] = $csrfValue;
    }
    
    public function exists(string $csrfKey) : bool
    {
        return isset($_SESSION['__csrf_'.$csrfKey]);
    }
    
    public function get(string $csrfKey) : string
    {
        return isset($_SESSION['__csrf_'.$csrfKey]) ? $_SESSION['__csrf_'.$csrfKey] : null;
    }
    
}