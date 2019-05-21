<?php

namespace HuaForms2;

class Facade
{
    protected $options = [];
    
    protected $handler;
    protected $renderer;
    
    protected $validationRun = false;
    protected $validationResult = null;
    
    public function __construct(string $formName, array $options)
    {
        $this->options = $options;
        $this->parse($formName);
        $this->handleCsrf();
    }
    
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
        $srcTime = filemtime($srcFile);
        if (!file_exists($tplFile) || !file_exists($jsonFile) || $srcTime > filemtime($tplFile) || $srcTime > filemtime($jsonFile)) {
            $parser = new Parser($srcFile);
            $parser->parse($tplFile, $jsonFile);
        }
        
        $this->handler = new Handler($jsonFile);
        $this->renderer = new Renderer($tplFile);
    }
    
    protected function handleCsrf() : void
    {
        $csrfKey = isset($this->options['csrfKey']) ? $this->options['csrfKey'] : 'csrf';
        $csrfClass = isset($this->options['csrfClass']) ? $this->options['csrfClass'] : \HuaForms2\Csrf\PhpSession::class;
        $csrfOptions = isset($this->options['csrfOptions']) ? $this->options['csrfOptions'] : [];
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
    
    public function setDefaults(array $values) : void
    {
        $this->renderer->setValues($values);
    }
    
    public function isSubmitted() : bool
    {
        return $this->handler->isSubmitted();
    }
    
    public function validate() : bool
    {
        if (!$this->validationRun) {
            $this->validationResult = $this->handler->isValid();
            if (!$this->validationResult) {
                $errors = $this->handler->getErrorMessages();
                $this->renderer->setErrors($errors);
                $this->renderer->setValues($this->handler->getSelectiveData());
            }
        }
        return $this->validationResult;
    }
    
    public function exportValues() : array
    {
        if ($this->validate()) {
            return $this->handler->getFormattedData();
        } else {
            return [];
        }
    }
    
    public function render() : string
    {
        return $this->renderer->render();
    }
    
}
