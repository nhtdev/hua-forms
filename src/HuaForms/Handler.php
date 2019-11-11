<?php

namespace HuaForms;

use HuaForms\Formatter;
use HuaForms\Validator;

/**
 * Form handler : class to process the form data an validation
 * 
 */
class Handler
{
    /**
     * Form information (type, field list, ...)
     * @var array
     */
    protected $conf;
    
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
     * True if the form validation has already been executed
     * @var bool
     */
    protected $validationRun = false;
    
    /**
     * Result of the form validation, if it has already been executed
     * @var bool|null
     */
    protected $validationResult = null;
    
    /**
     * Validation message (errors)
     * array('field_name' => ['Error msg 1', 'Error msg 2', ...], ...) 
     * @var array
     */
    protected $validationMsg = [];

    /**
     * Form values, formatted using fields formatters
     * Defined only if getFormattedData has been called
     * @var array|null
     */
    protected $formattedData = null;
    
    /**
     * Constructor
     * @param string $file File name containing the JSON description of the form
     * @throws \RuntimeException
     */
    public function __construct(string $file)
    {
        if (!is_readable($file)) {
            throw new \RuntimeException('File not found: '.$file);
        }
        $conf = json_decode(file_get_contents($file), true);
        if ($conf === null) {
            throw new \RuntimeException('Json decode of file "'.$file.'" error : '.json_last_error_msg());
        }
        $this->conf = $conf;
    }
    
    /**
     * Return the form information (type, field list, ...)
     * @return array
     */
    public function getDescription() : array
    {
        return $this->conf;
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
     * Return true if the form has been submitted
     * @return bool
     */
    public function isSubmitted() : bool
    {
        return ( $this->getSubmittedButton() !== null);
    }
    
    /**
     * Return the name of the submit button which has been clicked to submit the form
     * @return string|NULL
     */
    public function getSubmittedButton() : ?string
    {
        $data = $this->getRawData();
        foreach ($this->conf['submits'] as $submit) {
            $submitName = $submit['name'];
            if (isset($data[$submitName])) {
                return $data[$submitName];
            }
        }
        return null;
    }
    
    /**
     * Return the form submitted data, without any formatting or validation
     * @return array
     */
    public function getRawData() : array
    {
        if ($this->conf['method'] === 'get') {
            return $_GET;
        } else {
            return $_POST;
        }
    }
    
    /**
     * Look for the given field name in array and return its value
     * @param array $array
     * @param string $name
     * @return mixed
     */
    protected function getInArray(array $array, string $name)
    {
        $cleanName = str_replace('[]', '', $name);
        if (substr($name, -2) === '[]') {
            if (isset($array[$cleanName])) {
                return (array) $array[$cleanName];
            } else {
                return [];
            }
        } else {
            if (isset($array[$cleanName])) {
                if (is_array($array[$cleanName])) {
                    return array_pop($array[$cleanName]);
                } else {
                    return $array[$cleanName];
                }
            } else {
                return null;
            }
        }
    }
    
    /**
     * Set the falue of the given field name in array
     * @param array $array
     * @param string $name
     * @return mixed
     */
    protected function setInArray(array &$array, string $name, $value) : void
    {
        $cleanName = str_replace('[]', '', $name);
        if (substr($name, -2) === '[]') {
            if (isset($array[$cleanName])) {
                if (is_array($array[$cleanName])) {
                    if (is_array($value)) {
                        $array[$cleanName] = array_unique(array_merge($array[$cleanName], $value));
                    } else {
                        $array[$cleanName][] = $value;
                    }
                } else {
                    if (is_array($value)) {
                        $array[$cleanName] = array_unique(array_merge([$array[$cleanName]], $value));
                    } else {
                        $array[$cleanName] = [$array[$cleanName], $value];
                    }
                }
            } else {
                if (is_array($value)) {
                    $array[$cleanName] = $value;
                } else {
                    $array[$cleanName] = [$value];
                }
            }
        } else {
            if (is_array($value)) {
                $array[$cleanName] = array_pop($value);
            } else {
                $array[$cleanName] = $value;
            }
        }
    }
    
    /**
     * Return the form submitted data, without any formatting or validation,
     * but ignore the data which are not form fields
     * @return array
     */
    public function getSelectiveData() : array
    {
        $selectiveData = [];
        $rawData = $this->getRawData();
        foreach ($this->conf['fields'] as $field) {
            $name = $field['name'];
            $value = $this->getInArray($rawData, $name);
            $this->setInArray($selectiveData, $name, $value);
        }
        return $selectiveData;
    }
    
    /**
     * Return the form submitted data after formatting
     * @return array
     */
    public function getFormattedData() : array
    {
        $formatter = new Formatter();
        $rawData = $this->getRawData();
        if ($this->formattedData === null) {
            $this->formattedData = [];
            foreach ($this->conf['fields'] as $field) {
                $name = $field['name'];
                $value = $this->getInArray($rawData, $name);
                if (isset($field['formatters'])) {
                    foreach ($field['formatters'] as $oneFormat) {
                        $value = $formatter->format($oneFormat, $value);
                    }
                }
                $this->setInArray($this->formattedData, $name, $value);
            }
        }
        return $this->formattedData;
    }
    
    /**
     * Return true if the submitted data are valid
     * @return bool
     */
    public function isValid() : bool
    {
        if (!$this->validationRun) {
            $this->validate();
        }
        return $this->validationResult;
    }
    
    /**
     * Run the form validation, and store the result in the attributes "validation*" of the object
     */
    protected function validate() : void
    {
        $this->validationResult = true;
        $this->validationMsg = [];
        
        // CSRF validation
        $rawData = $this->getRawData();
        
        if (!isset($rawData[$this->csrfKey]) || $rawData[$this->csrfKey] !== $this->csrfValue) {
            $this->validationResult = false;
            $stdError = new StandardError();
            $this->validationMsg[''][] = $stdError->get('csrf');
            return;
        }
        
        $data = $this->getFormattedData();
        $validator = new Validator();
        foreach ($this->conf['fields'] as $field) {
            $name = $field['name'];
            $cleanName = str_replace('[]', '', $name);
            $value = $this->getInArray($data, $name);
            if (isset($field['rules'])) {
                foreach ($field['rules'] as $rule) {
                    if ($rule['type'] === 'required' || !empty($value) || $value === '0') { // Ignore rule if field is empty
                        $result = $validator->validate($rule, $value);
                        if ($result === true) {
                            // OK
                        } else if ($result === false) {
                            $this->validationResult = false;
                            $this->validationMsg[$cleanName][] = $this->validationErrorMessage($field, $rule);
                        } else {
                            $this->validationResult = false;
                            $this->validationMsg[$cleanName][] = $this->validationErrorMessage($field, $rule, $result);
                        }
                    }
                }
            }
        }
        
        $this->validationRun = true;
    }
    
    /**
     * Generate the error message for a given field and validation rule
     * @param array $field Field description
     * @param array $rule Rule description
     * @param string $validationResult Validation result (for validators returning various error messages)
     * @return string Error message
     */
    protected function validationErrorMessage(array $field, array $rule, string $validationResult=null) : string
    {
        $msg = null;
        
        if ($validationResult === null) {
            if (isset($rule['message'])) {
                $msg = $rule['message'];
            }
        } else {
            if (isset($rule[$validationResult.'-message'])) {
                $msg = $rule[$validationResult.'-message'];
            }
        }
        
        if ($msg === null) {
            $stdError = new StandardError();
            if ($validationResult === null) {
                $stdMessageType = $rule['type'];
            } else {
                $stdMessageType = $rule['type'].'-'.$validationResult;
            }
                
            $msg = $stdError->get($stdMessageType);
        }
        
        $replace = $rule;
        $replace['label'] = $field['label'];
        foreach ($replace as $key => $value) {
            $replaceTag = '{'.$key.'}';
            if (strpos($msg, $replaceTag) !== false) {
                if (is_array($value)) {
                    $value = implode(', ', $value);
                }
                $msg = str_replace($replaceTag, $value, $msg);
            }
        }
        return $msg;
    }
    
    /**
     * Return the form default values
     * @return array
     */
    public function getDefaultValues() : array
    {
        $result = [];
        foreach ($this->conf['fields'] as $field) {
            $name = $field['name'];
            $value = $field['value'];
            if ($value !== null) {
                $this->setInArray($result, $name, $value);
            }
        }
        return $result;
    }
    
    /**
     * Return the validation errors - array('field_name' => ['Error msg 1', 'Error msg 2', ...], ...)
     * The "isValid" method must be called before "getErrorMessages"
     * @return array
     */
    public function getErrorMessages() : array
    {
        return $this->validationMsg;
    }
    
}