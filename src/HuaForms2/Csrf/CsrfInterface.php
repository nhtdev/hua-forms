<?php

namespace HuaForms2\Csrf;

interface CsrfInterface
{
    
    public function __construct(array $params);
    
    public function set(string $csrfKey, string $csrfValue);
    
    public function exists(string $csrfKey) : bool;
    
    public function get(string $csrfValue) : string;
    
}