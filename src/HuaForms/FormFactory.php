<?php

namespace HuaForms;

class FormFactory
{
    protected static $global = null;
    
    protected $paths = [];
    protected $parser;
    protected $forms = [];
    protected $callback = [];
    
    public function __construct()
    {
        $this->parser = new Parser();
    }
    
    public static function global() : FormFactory
    {
        if (static::$global === null) {
            static::$global = new FormFactory();
        }
        return static::$global;
    }
    
    public function addPath(string $path) : FormFactory
    {
        $this->paths[] = $path;
        return $this;
    }
    
    public function get(string $formFile) : \HuaForms\Form
    {
        if (isset($this->forms[$formFile])) {
            return $this->forms[$formFile];
        } else {
            $form = $this->parse($formFile);
            $this->forms[$formFile] = $form;
            if (isset($this->callback[$formFile])) {
                $this->callback[$formFile]($form);
            }
            return $form;
        }
    }
    
    public function parse(string $formFile) : \HuaForms\Form
    {
        foreach ($this->paths as $path) {
            $fullFile = rtrim($path, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR . $formFile;
            if (file_exists($fullFile)) {
                return $this->parser->parse($fullFile);
            }
        }
        throw new \RuntimeException('Form not found "'.$formFile.'"'
                .' (form paths : '.implode(' ; ', $this->paths).')');
    }
    
    public function builder(string $fileName, Callable $callback) : FormFactory
    {
        $this->callback[$fileName] = $callback;
        return $this;
    }
}