<?php

namespace HuaForms;

/**
 * Class for easy usage of forms objects
 *
 */
class Facade
{
    /**
     * Options
     * @var array
     */
    protected $options = [];
    
    /**
     * Handler object
     * @var \HuaForms\Handler
     */
    protected $handler;
    
    /**
     * Renderer object
     * @var \HuaForms\Renderer
     */
    protected $renderer;
    
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
     * Constructor
     * @param string $formName Form name
     * @param array $options Form options
     */
    public function __construct(string $formName, array $options)
    {
        if (!isset($options['cache'])) {
            $options['cache'] = true;
        }
        $this->options = $options;
        $this->parse($formName);
        $this->handleCsrf();
    }
    
    /**
     * Return the form renderer object
     * @return \HuaForms\Renderer
     */
    public function renderer() : \HuaForms\Renderer
    {
        return $this->renderer;
    }
    
    /**
     * Return the form handler object
     * @return \HuaForms\Handler
     */
    public function handler() : \HuaForms\Handler
    {
        return $this->handler;
    }
    
    /**
     * Parse the given form (if needed) and generate the Handler and Renderer objects
     * @param string $formName
     */
    protected function parse(string $formName) : void
    {
        $srcPath = isset($this->options['formPath']) ? $this->options['formPath'] : 'forms/';
        $builtPath = isset($this->options['builtPath']) ? $this->options['builtPath'] : $srcPath . 'built/';
        
        $srcExtension = isset($this->options['srcExtension']) ? $this->options['srcExtension'] : 'form.html';
        $builtTplExtension = isset($this->options['builtTplExtension']) ? $this->options['builtTplExtension'] : 'form.php';
        $builtJsonExtension = isset($this->options['builtJsonExtension']) ? $this->options['builtJsonExtension'] : 'form.json';
        
        $srcFile = $srcPath . $formName . '.' . $srcExtension;
        $tplFile = $builtPath . $formName . '.' . $builtTplExtension;
        $jsonFile = $builtPath . $formName . '.' . $builtJsonExtension;
        
        if (!file_exists($srcFile)) {
            throw new \RuntimeException('Form not found: '.$formName);
        }
        
        $srcTime = filemtime($srcFile);
        if (!$this->options['cache'] 
            || !file_exists($tplFile) 
            || !file_exists($jsonFile) 
            || $srcTime > filemtime($tplFile) 
            || $srcTime > filemtime($jsonFile)) {
            $parser = new Parser($srcFile);
            $parser->parse($tplFile, $jsonFile);
        }
        
        $this->handler = new Handler($jsonFile);
        $this->renderer = new Renderer($tplFile);
        $this->renderer->setValues($this->handler->getDefaultValues());
    }
    
    /**
     * Generate and handle the CSRF token in both Handler and Renderer objects.
     */
    protected function handleCsrf() : void
    {
        $csrfKey = $this->options['csrfKey'] ?? '_csrf_token_';
        $csrfClass = $this->options['csrfClass'] ?? \HuaForms\ServerStorage\PhpSession::class;
        $csrfOptions = $this->options['csrfOptions'] ?? [];
        $csrf = new $csrfClass($csrfOptions);
        
        if (empty($_POST) || !$csrf->exists($csrfKey)) {
            // Generate new CSRF
            $csrfValue = base64_encode( openssl_random_pseudo_bytes(32));
            $csrf->set($csrfKey, $csrfValue);
        } else {
            $csrfValue = $csrf->get($csrfKey);
        }
        
        $this->handler->setCsrf($csrfKey, $csrfValue);
        $this->renderer->setCsrf($csrfKey, $csrfValue);
    }
    
    /**
     * Set the default values of the form field
     * @param array $values Form default values
     */
    public function setDefaults(array $values) : void
    {
        $this->renderer->setValues($values);
    }
    
    /**
     * Return true if the form has been submitted
     * @return bool
     */
    public function isSubmitted() : bool
    {
        return $this->handler->isSubmitted();
    }
    
    /**
     * Return true if the submitted data are valid
     * @return bool
     */
    public function validate() : bool
    {
        if (!$this->validationRun) {
            $this->validationResult = $this->handler->isValid();
            if (!$this->validationResult) {
                $errors = $this->handler->getErrorMessages();
                $this->renderer->setErrors($errors);
                $this->renderer->setValues($this->handler->getSelectiveData());
            } else {
                $this->renderer->setValues($this->handler->getFormattedData());
            }
        }
        return $this->validationResult;
    }
    
    /**
     * Return the form submitted data after formatting
     * @return array
     */
    public function exportValues() : array
    {
        if ($this->validate()) {
            return $this->handler->getFormattedData();
        } else {
            return [];
        }
    }
    
    /**
     * Render the HTML code for displaying the form
     * @return string HTML
     */
    public function render() : string
    {
        return $this->renderer->render();
    }
    
    /**
     * Return the form information (type, field list, ...)
     * @return array
     */
    public function getDescription() : array
    {
        return $this->handler->getDescription();
    }
    
}
