<?php

namespace HuaForms;

/**
 * Form factory : for easy creation of Form objects
 * @author x
 *
 */
class FormFactory
{
    /**
     * Global instance of FormFactory (if used as a singleton)
     * @var FormFactory
     */
    protected static $global = null;
    
    /**
     * Paths where form files are located
     * @var array
     */
    protected $paths = [];
    
    /**
     * Form parser
     * @var Parser
     */
    protected $parser;
    
    /**
     * Parsed forms : ['file_name' => $object]
     * @var array
     */
    protected $forms = [];
    
    /**
     * List of functions that will be called after form parsing
     * ['file_name' => [$function1, $function2] ]
     * @var array
     */
    protected $callback = [];
    
    /**
     * Constructor
     */
    public function __construct()
    {
        $this->parser = new Parser();
    }
    
    /**
     * Returns the global instance of FormFactory (used as a singleton)
     * @return FormFactory
     */
    public static function global() : FormFactory
    {
        if (static::$global === null) {
            static::$global = new FormFactory();
        }
        return static::$global;
    }
    
    /**
     * Add one path where form files are located
     * @param string $path Path
     */
    public function addPath(string $path) : void
    {
        $this->paths[] = $path;
    }
    
    /**
     * Parse and create a new Form using the specified file.
     * If the file has already been parsed, returns the existing instance.
     * @param string $formFile
     * @return \HuaForms\Form
     */
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
    
    /**
     * Parse and create a new Form using the specified file.
     * @param string $formFile
     * @throws \RuntimeException
     * @return \HuaForms\Form
     */
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
    
    /**
     * Defines a function that will be called after form parsing
     * @param string $fileName File containing the form definition
     * @param callable $callback Function to call if the file is parsed
     */
    public function builder(string $fileName, Callable $callback) : void
    {
        $this->callback[$fileName] = $callback;
    }
}