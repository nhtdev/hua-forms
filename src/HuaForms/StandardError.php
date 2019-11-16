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
        'csrf'                => 'Invalid CSRF token',
        'required'            => '{label}: field is required',
        'maxlength'           => '{label}: maximum {maxlength} characters',
        'minlength'           => '{label}: minimum {minlength} characters',
        'inarray'             => '{label}: value is not allowed',
        'email'               => '{label}: invalid email',
        'url'                 => '{label}: invalid url',
        'color'               => '{label}: invalid color',
        'number'              => '{label}: value is not a valid number',
        'number-min'          => '{label}: value must be greater than or equal to {min}',
        'number-max'          => '{label}: value must be less than or equal to {max}',
        'number-step'         => '{label}: value is not allowed',
        'month'               => '{label}: value is not a valid month',
        'month-min'           => '{label}: value must be greater than or equal to {min}',
        'month-max'           => '{label}: value must be less than or equal to {max}',
        'week'                => '{label}: value is not a valid week number',
        'week-min'            => '{label}: value must be greater than or equal to {min}',
        'week-max'            => '{label}: value must be less than or equal to {max}',
        'date'                => '{label}: value is not a valid date',
        'date-min'            => '{label}: value must be greater than or equal to {min}',
        'date-max'            => '{label}: value must be less than or equal to {max}',
        'time'                => '{label}: value is not a valid time',
        'time-min'            => '{label}: value must be greater than or equal to {min}',
        'time-max'            => '{label}: value must be less than or equal to {max}',
        'time-step'           => '{label}: value is not allowed',
        'time-inverse'        => '{label}: value must not be between {max} and {min}',
        'datetime-local'      => '{label}: value is not a valid date',
        'datetime-local-min'  => '{label}: value must be greater than or equal to {min}',
        'datetime-local-max'  => '{label}: value must be less than or equal to {max}',
        'datetime-local-step' => '{label}: value is not allowed',
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
            throw new \InvalidArgumentException('StandardError : invalid rule type "'.$ruleType.'"');
        }
        return $this->msg[$ruleType];
    }
    
}