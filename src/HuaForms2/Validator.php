<?php

namespace HuaForms2;

class Validator
{
    
    public function validate(array $rule, $value) : bool
    {
        if (empty($rule['type'])) {
            throw new \InvalidArgumentException('Rule type is empty');
        }
        $method = 'validate'.ucfirst($rule['type']);
        if (!method_exists($this, $method)) {
            throw new \InvalidArgumentException('Invalid rule type "'.$rule['type'].'"');
        }
        return $this->$method($rule, $value);
    }
    
    public function validateRequired(array $rule, $value) : bool
    {
        if (empty($value) && $value !== '0') {
            return false;
        } else {
            return true;
        }
    }
    
    public function validateMaxlength(array $rule, $value) : bool
    {
        if (!is_string($value)) {
            throw new \InvalidArgumentException('Rule maxlength : value must be a string');
        }
        if (!isset($rule['maxlength'])) {
            throw new \InvalidArgumentException('Rule maxlength : missing param "maxlength"');
        }
        if (mb_strlen($value) > $rule['maxlength']) {
            return false;
        } else {
            return true;
        }
    }
    
    public function validateInArray(array $rule, $value) : bool
    {
        if (!isset($rule['values'])) {
            throw new \InvalidArgumentException('Rule maxlength : missing param "values"');
        }
        $values = $rule['values'];
        if (!is_array($values)) {
            throw new \InvalidArgumentException('Rule maxlength : param "values" must be an array');
        }
        if (is_array($value)) {
            foreach ($value as $oneValue) {
                if (!in_array($oneValue, $values)) {
                    return false;
                }
            }
            return true;
        } else {
            return in_array($value, $values);
        }
    }
    
}