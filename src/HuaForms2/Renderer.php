<?php

namespace HuaForms2;

class Renderer
{
    protected $file;
    protected $values = [];
    protected $errors = [];
    protected $csrfKey = '';
    protected $csrfValue = '';
    
    public function __construct(string $file)
    {
        $this->file = $file;
        if (!is_readable($file)) {
            throw new \RuntimeException('File not found: '.$file);
        }
    }

    public function setValues(array $values) : void
    {
        $this->values = $values;
    }
    
    public function setErrors(array $errors) : void
    {
        $this->errors = $errors;
    }
    
    public function setCsrf(string $key, string $value) : void
    {
        $this->csrfKey = $key;
        $this->csrfValue = $value;
    }
    
    public function getCsrfKey() : string
    {
        return $this->csrfKey;
    }
    
    public function getCsrfValue() : string
    {
        return $this->csrfValue;
    }
    
    public function render() : string
    {
        ob_start();
        require($this->file);
        $output = ob_get_clean();
        return $output;
    }
    
    public function getValue($name) : string
    {
        if (isset($this->values[$name])) {
            return $this->values[$name];
        } else {
            return '';
        }
    }
    
    protected function hasValue($name, $value) : bool
    {
        if (isset($this->values[$name])) {
            if (is_array($this->values[$name])) {
                return in_array($value, $this->values[$name]);
            } else {
                return $value == $this->values[$name];
            }
        } else {
            return false;
        }
    }
    
    public function attr($attrName, $name, $value) : string
    {
        if ($this->hasValue($name, $value)) {
            return ' '.$attrName.' ';
        } else {
            return '';
        }
    }
    
    public function attrSelected($name, $value) : string
    {
        return $this->attr('selected', $name, $value);
    }
    
    public function attrChecked($name, $value) : string
    {
        return $this->attr('checked', $name, $value);
    }
    
    public function hasErrors() : bool
    {
        return !empty($this->errors);
    }
    
    public function getErrorsByField() : array
    {
        return $this->errors;
    }
    
    public function getErrors() : array
    {
        $allErrors = [];
        foreach ($this->errors as $fieldErrors) {
            foreach ($fieldErrors as $error) {
                $allErrors[] = $error;
            }
        }
        return $allErrors;
    }
    
    public function getErrorsAsString() : string
    {
        return implode("\n", $this->getErrors());
    }
    
}