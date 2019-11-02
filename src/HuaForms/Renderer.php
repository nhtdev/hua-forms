<?php

namespace HuaForms;

/**
 * Form renderer
 *
 */
class Renderer
{
    /**
     * File name containing the php template of the form
     * @var string
     */
    protected $file;
    
    /**
     * Form values - array('name' => 'value')
     * @var array
     */
    protected $values = [];
    
    /**
     * Form errors - array('field_name' => ['Error msg 1', 'Error msg 2', ...], ...)
     * @var array 
     */
    protected $errors = [];
    
    /**
     * Key of the CSRF token
     * @var string
     */
    protected $csrfKey = '';
    
    /**
     * Value of the CSRF token
     * @var string
     */
    protected $csrfValue = '';
    
    /**
     * Constructor
     * @param string $file File name containing the php template of the form
     * @throws \RuntimeException
     */
    public function __construct(string $file)
    {
        $this->file = $file;
        if (!is_readable($file)) {
            throw new \RuntimeException('File not found: '.$file);
        }
    }
    
    /**
     * Set the form values
     * @param array $values Form values - array ('name' => 'value')
     */
    public function setValues(array $values) : void
    {
        $this->values = array_merge($this->values, $values);
    }
    
    /**
     * Set the form errors
     * @param array $errors Form errors - array('field_name' => ['Error msg 1', 'Error msg 2', ...], ...)
     */
    public function setErrors(array $errors) : void
    {
        $this->errors = $errors;
    }
    
    /**
     * Set the key and value of the CSRF token
     * @param string $key Key of the CSRF token
     * @param string $value Value of the CSRF token
     */
    public function setCsrf(string $key, string $value) : void
    {
        $this->csrfKey = $key;
        $this->csrfValue = $value;
    }
    
    /**
     * Returns the key of CSRF token
     * @return string
     */
    public function getCsrfKey() : string
    {
        return $this->csrfKey;
    }
    
    /**
     * Returns the value of CSRF token
     * @return string
     */
    public function getCsrfValue() : string
    {
        return $this->csrfValue;
    }
    
    /**
     * Render the form and return the generated HTML
     * @return string
     */
    public function render() : string
    {
        ob_start();
        require($this->file);
        $output = ob_get_clean();
        return $output;
    }
    
    /**
     * Return the value of a given form field
     * @param string $name Field name
     * @return string Field value
     */
    public function getValue(string $name) : string
    {
        if (isset($this->values[$name])) {
            return $this->values[$name];
        } else {
            return '';
        }
    }
    
    /**
     * Check if a field has the specified value (non type-strict test)
     * @param string $name Field name
     * @param mixed $value Tested value
     * @return bool
     */
    protected function hasValue(string $name, $value) : bool
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
    
    /**
     * Generate the HTML attribute, if a value has been given to a field
     * @param string $attrName Attribute name
     * @param string $name Field name
     * @param mixed $value Tested value
     * @return string
     */
    public function attr(string $attrName, string $name, $value) : string
    {
        if ($this->hasValue($name, $value)) {
            return ' '.$attrName;
        } else {
            return '';
        }
    }
    
    /**
     * Generate the HTML attribute "selected", if a value has been given to a field
     * @param string $name Field name
     * @param mixed $value Tested value
     * @return string
     */
    public function attrSelected(string $name, $value) : string
    {
        return $this->attr('selected', $name, $value);
    }
    
    /**
     * Generate the HTML attribute "checked", if a value has been given to a field
     * @param string $name Field name
     * @param mixed $value Tested value
     * @return string
     */
    public function attrChecked(string $name, $value) : string
    {
        if ($value === true) {
            $value = 'on';
        }
        if ($value === false) {
            $value = '';
        }
        return $this->attr('checked', $name, $value);
    }
    
    /**
     * Return true if the form has any error
     * @return bool
     */
    public function hasErrors() : bool
    {
        return !empty($this->errors);
    }
    
    /**
     * Return the form errors grouped by field - array('field_name' => ['Error msg 1', 'Error msg 2', ...], ...)
     * @return array
     */
    public function getErrorsByField() : array
    {
        return $this->errors;
    }
    
    /**
     * Return all the form errors - array('Error msg 1', 'Error msg 2', ...)
     * @return array
     */
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
    
    /**
     * Return all the form errors as a string - "Errorm msg1\nError msg2..."
     * @return string
     */
    public function getErrorsAsString() : string
    {
        return implode("\n", $this->getErrors());
    }
    
}