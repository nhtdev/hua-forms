<?php

namespace HuaForms2;

use HuaForms2\Formatter;
use HuaForms2\Validator;

class Handler
{
    
    protected $conf;
    protected $csrfKey = '';
    protected $csrfValue = '';
    
    protected $validationRun = false;
    protected $validationResult = null;
    protected $validationMsg = [];
    
    protected $formattedData = null;
    
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
    
    public function getDescription() : array
    {
        return $this->conf;
    }
    
    public function setCsrf(string $key, string $value) : void
    {
        $this->csrfKey = $key;
        $this->csrfValue = $value;
    }
    
    public function isSubmitted() : bool
    {
        return ( $this->getSubmittedButton() !== null);
    }
    
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
    
    public function getRawData() : array
    {
        if ($this->conf['method'] === 'get') {
            return $_GET;
        } else {
            return $_POST;
        }
    }
    
    public function getSelectiveData() : array
    {
        $selectiveData = [];
        $rawData = $this->getRawData();
        foreach ($this->conf['fields'] as $field) {
            $name = $field['name'];
            if (isset($rawData[$name])) {
                $selectiveData[$name] = $rawData[$name];
            } else {
                $selectiveData[$name] = null;
            }
        }
        return $selectiveData;
    }
    
    public function getFormattedData() : array
    {
        $formatter = new Formatter();
        $rawData = $this->getRawData();
        if ($this->formattedData === null) {
            $this->formattedData = [];
            foreach ($this->conf['fields'] as $field) {
                $name = $field['name'];
                if (isset($rawData[$name])) {
                    $value = $rawData[$name];
                    if (isset($field['formatters'])) {
                        foreach ($field['formatters'] as $oneFormat) {
                            $value = $formatter->format($oneFormat, $value);
                        }
                    }
                    $this->formattedData[$name] = $value;
                } else {
                    $this->formattedData[$name] = null;
                }
            }
        }
        return $this->formattedData;
    }
    
    public function isValid() : bool
    {
        if (!$this->validationRun) {
            $this->validate();
        }
        return $this->validationResult;
    }
    
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
            $value = $data[$name];
            if (isset($field['rules'])) {
                foreach ($field['rules'] as $rule) {
                    if ($rule['type'] === 'required' || !empty($value) || $value === '0') { // Ignore rule if field is empty
                        if (!$validator->validate($rule, $value)) {
                            $this->validationResult = false;
                            $this->validationMsg[$name][] = $this->validationErrorMessage($field, $rule);
                        }
                    }
                }
            }
        }
        
        $this->validationRun = true;
    }
    
    protected function validationErrorMessage(array $field, array $rule) : string
    {
        if (isset($rule['message'])) {
            return $rule['message'];
        } else {
            $stdError = new StandardError();
            $msg = $stdError->get($rule['type']);
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
    }
    
    public function getErrorMessages() : array
    {
        return $this->validationMsg;
    }
    
}