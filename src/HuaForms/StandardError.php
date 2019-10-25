<?php

namespace HuaForms;

/**
 * Standard error messages for validation errors
 *
 */
class StandardError
{
    /**
     * Standard error messages : ['rule_type' => 'Message']
     * @var array
     */
    protected $msg = [
        'csrf'          => 'Invalid CSRF token',
        'required'      => '{label}: field is required',
        'maxlength'     => '{label}: maximum {maxlength} characters',
        'inarray'       => '{label}: value is not in the authorized values list ({values})',
    ];
    
    /**
     * Return the error message for the given rule type
     * @param string $ruleType Rule type
     * @throws \InvalidArgumentException
     * @return string Error message
     */
    public function get(string $ruleType) : string
    {
        if (!isset($this->msg[$ruleType])) {
            throw new \InvalidArgumentException('Invalid rule type "'.$ruleType.'"');
        }
        return $this->msg[$ruleType];
    }
    
}