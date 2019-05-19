<?php

namespace HuaForms2;

class StandardError
{
    protected $msg = [
        'csrf'          => 'Invalid CSRF token',
        'required'      => '{label}: field is required',
        'maxlength'     => '{label}: maximum {maxlength} characters',
        'inArray'       => '{label}: value is not in the authorized values list ({values})',
    ];
    
    public function get(string $ruleType) : string
    {
        if (!isset($this->msg[$ruleType])) {
            throw new \InvalidArgumentException('Invalid rule type "'.$ruleType.'"');
        }
        return $this->msg[$ruleType];
    }
    
}