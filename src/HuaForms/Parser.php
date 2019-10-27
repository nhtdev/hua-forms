<?php

namespace HuaForms;

/**
 * Form parser : parse an HTML file, and generate the PHP template + JSON structure information
 * The HTML file can then be used by the "Renderer" class, and the JSON by the "Handler" class
 */
class Parser
{
    /**
     * File name containing the HTML of the form
     * @var string
     */
    protected $inputFile;
    
    /**
     * Internal counter for submit buttons
     * @var integer
     */
    protected $submitCpt = 1;
    
    /**
     * Tag used for injecting PHP code into the template file
     * @var string
     */
    const PHP_CODE = 'PHP_CODE';
    
    /**
     * Constructor
     * @param string $inputFile File name containing the HTML of the form
     * @throws \RuntimeException
     */
    public function __construct(string $inputFile)
    {
        $this->inputFile = $inputFile;
        if (!is_readable($inputFile)) {
            throw new \RuntimeException('File not found: '.$inputFile);
        }
    }
    
    /**
     * Execute the form parsing, and save the PHP template
     * + JSON structure information to the specified files
     * @param string $outputPhp Name of the file where the php template of the form will be saved
     * @param string $outputJson Name of the file where the JSON description of the form will be saved
     */
    public function parse(string $outputPhp, string $outputJson) : void
    {
        // 1- Parse DOM HTML
        $dom = new \DOMDocument();
        $dom->loadHTML(
            '<?xml encoding="utf-8" ?>' . file_get_contents($this->inputFile), 
            LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD);

        // 2- Modify DOM HTML
        $this->modifyDom($dom);
        
        // 3- Build JSON From DOM
        $data = $this->buildJsonFromDom($dom);
        
        // 4- Write JSON
        $this->writeJson($data, $outputJson);
        
        // 5- Write DOM
        $this->writeDom($dom, $outputPhp);
    }
    
    /**
     * Tool method : Recursively call the $fct function on the DOM node and all of its children
     * @param \DOMNode $node DOM node
     * @param callable $fct Callback function - will receive the \DOMElement as parameter
     */
    protected function walkElements(\DOMNode $node, callable $fct) : void
    {
        if ($node instanceof \DOMElement) {
            $result = $fct($node);
        } else {
            $result = true;
        }
        
        if ($result !== false && $node->hasChildNodes()) {
            foreach ($node->childNodes as $childNode) {
                $this->walkElements($childNode, $fct);
            }
        }
    }
    
    /**
     * Tool method : Search for a given node name in all the ancestors of a DOM node
     * @param \DomNode $node DOM node
     * @param string $nodeName Node name to look for
     * @return \DomNode|NULL
     */
    protected function findClosest(\DomNode $node, string $nodeName) : ?\DomNode
    {
        if ($node->parentNode === null) {
            return null;
        }
        if ($node->parentNode->nodeName === $nodeName) {
            return $node->parentNode;
        }
        return $this->findClosest($node->parentNode, $nodeName);
    }
    
    /**
     * Run all the HTML modification so that the form will look better! 
     * @param \DOMDocument $dom
     */
    protected function modifyDom(\DOMDocument $dom) : void
    {
        $this->addTypeToInput($dom);
        $this->addIdAttributes($dom);
        $this->addAlertDivIfNotFound($dom);
        $this->addForInLabelNodes($dom);
    }
    
    /**
     * Add a "type=text" attribute input without "type" attribute
     * @param \DOMDocument $dom
     */
    protected function addTypeToInput(\DOMDocument $dom) : void
    {
        $this->walkElements($dom, function (\DOMElement $node) {
            if ($node->nodeName === 'input' && !$node->hasAttribute('type')) {
                $node->setAttribute('type', 'text');
            }
        });
    }
    
    /**
     * Add an "id" attribute to all form elements
     * @param \DOMDocument $dom
     */
    protected function addIdAttributes(\DOMDocument $dom) : void
    {
        $this->walkElements($dom, function (\DOMElement $node) {
            if ($this->isInputNode($node) || $this->isButtonNode($node) || $this->isSubmitNode($node)) {
                if ($node->hasAttribute('name') && !$node->hasAttribute('id')) {
                    $id = str_replace(['[', ']'], '', $node->getAttribute('name'));
                    $node->setAttribute('id', $id);
                }
            }
        });
    }
    
    /**
     * Create a div with a "form-errors" attribute, if none is already present in the HTML
     * @param \DOMDocument $dom
     */
    protected function addAlertDivIfNotFound(\DOMDocument $dom) : void
    {
        $found = false;
        $this->walkElements($dom, function (\DOMElement $node) use (&$found) {
            if ($node->hasAttribute('form-errors')) {
                $found = true;
            }
        });
        if (!$found) {
            $this->walkElements($dom, function (\DOMElement $node) {
                if ($this->isFormNode($node)) {
                    
                    $div = $node->ownerDocument->createElement('div');
                    $div->setAttribute('form-errors', 'true');
                    $node->insertBefore($div, $node->firstChild);
                    
                    $node->insertBefore($node->ownerDocument->createTextNode("\n"), $div);
                }
            });
        }
    }
    
    /**
     * Add "for" attribute in every <label> nodes
     * @param \DOMDocument $dom
     */
    protected function addForInLabelNodes(\DOMDocument $dom) : void
    {
        $this->walkElements($dom, function (\DOMElement $node) {
            if ($this->isInputNode($node) && $node->hasAttribute('id')) {
                $label = $this->findLabelNode($node);
                if ($label !== null) {
                    if (!$label->hasAttribute('for')) {
                        $label->setAttribute('for', $node->getAttribute('id'));
                    }
                }
            }
        });
    }
    
    /**
     * Generate and returns the whole JSON information of the form by reading the DOM structure 
     * @param \DOMDocument $dom
     * @return array
     */
    protected function buildJsonFromDom(\DOMDocument $dom) : array
    {
        $data = [
            'method' => '',
            'fields' => [],
            'submits' => []
        ];
        
        $this->walkElements($dom, function (\DOMElement $node) use (&$data) {
            
            if ($this->isFormNode($node)) {
                $this->buildJsonForm($node, $data);
                
            } else if ($this->isInputNode($node)) {
                if ($node->nodeName === 'input') {
                    if ($node->hasAttribute('type')) {
                        $type = $node->getAttribute('type');
                    } else {
                        $type = 'text';
                    }
                } else {
                    $type = $node->nodeName; // textarea, select, ...
                }
                $this->buildJsonInput($type, $node, $data);
                
            } else if ($this->isButtonNode($node)) {
                $this->buildJsonButton($node, $data);
                
            } else if ($this->isSubmitNode($node)) {
                $this->buildJsonSubmit($node, $data);
            }
            
            // Do not parse DOM structure of select elements
            if ($node->nodeName === 'select') {
                return false;
            } else {
                return true;
            }
        });
        return $data;
    }
    
    /**
     * Return true if the given DOM node is a form
     * @param \DOMElement $node
     * @return bool
     */
    protected function isFormNode(\DOMElement $node) : bool
    {
        if ($node->nodeName === 'form') {
            return true;
        } else {
            return false;
        }
    }
    
    /**
     * Return true if the given DOM node is an input field
     * @param \DOMElement $node
     * @return bool
     */
    protected function isInputNode(\DOMElement $node) : bool
    {
        if ($node->nodeName === 'input') {
            if ($node->hasAttribute('type')) {
                $type = $node->getAttribute('type');
                if ($type === 'button' || $type === 'submit') {
                    return false;
                }
            }
            return true;
        } else if ($node->nodeName === 'textarea'
            || $node->nodeName === 'select') {
            return true;
        } else {
            return false;
        }
    }
    
    /**
     * Return true if the given DOM node is a form button
     * @param \DOMElement $node
     * @return bool
     */
    protected function isButtonNode(\DOMElement $node) : bool
    {
        if ($node->nodeName === 'button') {
            if ($node->hasAttribute('type')) {
                $type = $node->getAttribute('type');
                if ($type === 'submit') {
                    return false;
                }
                return true;
            } else {
                return true;
            }
            
        } else if ($node->nodeName === 'input') {
            if ($node->hasAttribute('type')) {
                $type = $node->getAttribute('type');
                if ($type === 'button') {
                    return true;
                }
            }
            return false;
        } else {
            return false;
        }
    }
    
    /**
     * Return true if the given DOM node is a form submit button
     * @param \DOMElement $node
     * @return bool
     */
    protected function isSubmitNode(\DOMElement $node) : bool
    {
        if ($node->nodeName === 'button') {
            return !$this->isButtonNode($node);
            
        } else if ($node->nodeName === 'input') {
            if ($node->hasAttribute('type')) {
                $type = $node->getAttribute('type');
                if ($type === 'submit') {
                    return true;
                }
            }
            return false;
        } else {
            return false;
        }
    }
    
    /**
     * Return true if the given DOM node is a field label
     * @param \DOMElement $node
     * @return bool
     */
    protected function isLabelNode(\DOMElement $node) : bool
    {
        if ($node->nodeName === 'label') {
            return true;
        } else {
            return false;
        }
    }
    
    /**
     * Add information about the form itself in the JSON object
     * @param \DOMElement $node DOM node of the form
     * @param array $data JSON object which will be modified by reference
     */
    protected function buildJsonForm(\DOMElement $node, array &$data) : void
    {
        if ($node->hasAttribute('method')) {
            $data['method'] = $node->getAttribute('method');
        }
    }
    
    /**
     * Add information about a form button in the JSON object
     * @param \DOMElement $node DOM node of the button
     * @param array $data JSON object which will be modified by reference
     */
    protected function buildJsonButton(\DOMElement $node, array &$data) : void
    {
        // Do nothing
    }
    
    /**
     * Add information about a form submit button in the JSON object
     * @param \DOMElement $node DOM node of the submit button
     * @param array $data JSON object which will be modified by reference
     */
    protected function buildJsonSubmit(\DOMElement $node, array &$data) : void
    {
        // Label
        if ($node->nodeName === 'input') {
            $label = '';
            if ($node->hasAttribute('value')) {
                $label = $node->getAttribute('value');
            }
        } else { // <button>
            $label = $node->nodeValue;
        }
        
        // Name
        if ($node->hasAttribute('name')) {
            $name = $node->getAttribute('name');
        } else {
            $name = 'submit'.$this->submitCpt;
        }
        $this->submitCpt++;
        
        // Save
        $data['submits'][] = ['label' => $label, 'name' => $name];
    }
    
    /**
     * Add information about a form element (input) in the JSON object
     * @param string $type Field type : "text", "select", "textarea", "date", ...
     * @param \DOMElement $node DOM node of the element
     * @param array $data JSON object which will be modified by reference
     */
    protected function buildJsonInput(string $type, \DOMElement $node, array &$data) : void
    {
        
        // Name
        if (!$node->hasAttribute('name')) {
            // Field is not sent to server when submitted
            $this->triggerWarning('Input field has no "name" attribute', $node);
            return;
        }
        $name = $node->getAttribute('name');
        
        // Check type
        if (!in_array($type, ['text', 'select'])) {
            $this->triggerWarning('Ivalid input type "'.$type.'"', $node);
            $type = 'text';
        }
        
        // Label
        $labelNode = $this->findLabelNode($node);
        if ($labelNode === null) {
            $label = '';
        } else {
            $label = $labelNode->nodeValue;
        }
        
        // Formatters
        $formatters = $this->buildJsonFormatters($type, $node);
        
        // Rules
        $rules = $this->buildJsonRules($type, $node);
        
        // Save
        $data['fields'][] = [
            'label' => $label,
            'name' => $name,
            'type' => $type,
            'formatters' => $formatters,
            'rules' => $rules
        ];
        
    }
    
    /**
     * Generate a php warning
     * @param string $warning Message
     * @param \DOMElement $node DOM Node
     */
    protected function triggerWarning(string $warning, \DOMElement $node) : void
    {
        trigger_error($warning.' in form "'.$this->inputFile.'" line '.$node->getLineNo(), E_USER_WARNING);
    }
    
    /**
     * Generate and return the formatters for an element of the DOM
     * @param string $type Field type : "text", "select", "textarea", "date", ...
     * @param \DOMElement $node
     * @return array
     */
    protected function buildJsonFormatters(string $type, \DOMElement $node) : array
    {
        $formatters = [];
        if ($node->hasAttribute('trim')) {
            $formatters[] = [
                'type' => 'trim'
            ];
            $node->removeAttribute('trim');
        }
        return $formatters;
    }
    
    /**
     * Generate and return the validation rules for an element of the DOM
     * @param string $type Field type : "text", "select", "textarea", "date", ...
     * @param \DOMElement $node
     * @return array
     */
    protected function buildJsonRules(string $type, \DOMElement $node) : array
    {
        $rules = [];
        
        if ($node->hasAttribute('required')) {
            $rule = ['type' => 'required'];
            $rules[] = $rule;
            // Keep required attribute in html
        }
        
        if ($node->hasAttribute('maxlength')) {
            $value = (int) $node->getAttribute('maxlength');
            $rule = ['type' => 'maxlength', 'maxlength' => $value];
            $rules[] = $rule;
            // Keep maxlength attribute in html
        }
        
        if ($node->hasAttribute('inarray')) {
            $values = explode(',', $node->getAttribute('inarray'));
            $rule = ['type' => 'inarray', 'values' => $values];
            $rules[] = $rule;
            $node->removeAttribute('inarray');
        }
        
        if ($node->nodeName === 'select') {
            $optionsValues = [];
            $this->walkElements($node, function (\DOMElement $element) use (&$optionsValues) {
                if ($element->nodeName === 'option') {
                    if ($element->hasAttribute('value')) {
                        $value = $element->getAttribute('value');
                    } else {
                        $value = trim($element->textContent);
                    }
                    if (!empty($value)) {
                        $optionsValues[] = $value;
                    }
                }
            });
            $rule = [
                'type' => 'inarray',
                'values' => $optionsValues
            ];
            $rules[] = $rule;
        }
        
        // Override rule-message for each rule
        foreach ($rules as &$rule) {
            $ruleType = strtolower($rule['type']);
            if ($node->hasAttribute($ruleType.'-message')) {
                $rule['message'] = $node->getAttribute($ruleType.'-message');
                $node->removeAttribute($ruleType.'-message');
            }
        }
        
        return $rules;
    }
    
    /**
     * Search form DOM Node of the label associated to an input field
     * @param \DOMElement $input
     * @return \DOMNode|NULL
     */
    protected function findLabelNode(\DOMElement $input) : ?\DOMNode
    {
        // TODO : + intelligent
        
        // Previous sibling
        $prev = $input;
        do {
            $prev = $prev->previousSibling;
        } while ($prev !== null && $prev->nodeName === '#text');
        if ($prev !== null && $prev->nodeName === 'label') {
            return $prev;
        }
        
        // Not found
        return null;
    }
    
    /**
     * Save the JSON description of the form on disk file
     * @param array $data JSON description of the form
     * @param string $file File name
     */
    protected function writeJson(array $data, string $file) : void
    {
        file_put_contents($file, json_encode($data, JSON_PRETTY_PRINT));
    }
    
    /**
     * Save the PHP template of the form on disk file
     * @param \DOMDocument $dom DOM document representing the form template
     * @param string $file File name
     */
    protected function writeDom(\DOMDocument $dom, string $file) : void
    {
        $this->injectValuesIntoDom($dom);
        $this->injectSelectedIntoDom($dom);
        $this->injectCsrfIntoDom($dom);
        $this->injectCodeAroundFormErrors($dom);
        
        $html = $dom->saveXML($dom->documentElement);
        
        $html = $this->convertToHtml5($html);
        $html = $this->replacePhpCodeInHtml($html);
        
        file_put_contents($file, $html);
    }
    
    /**
     * Search DOM document for all input fields, and add to each of them
     * PHP code for setting their values
     * @param \DOMDocument $dom
     */
    protected function injectValuesIntoDom(\DOMDocument $dom) : void
    {
        $this->walkElements($dom, function (\DOMElement $node) {
            if ($node->nodeName === 'input' && $node->hasAttribute('name')) {
                $name = $node->getAttribute('name');
                $phpCode = 'echo htmlentities($this->getValue('.$this->quotePhpVar($name).'));';
                $node->setAttribute('value', self::PHP_CODE.'="'.$phpCode.'"');
            }
        });
    }
    
    /**
     * Search DOM document for all <select> fields, and add to each of them
     * PHP code for setting their selected option
     * @param \DOMDocument $dom
     */
    protected function injectSelectedIntoDom(\DOMDocument $dom) : void
    {
        $this->walkElements($dom, function (\DOMElement $node) {
            if ($node->nodeName === 'option') {
                $value = $node->getAttribute('value');
                $select = $this->findClosest($node, 'select');
                if ($select !== null && $select->hasAttribute('name')) {
                    $name = $select->getAttribute('name');
                    $phpCode = 'echo $this->attrSelected('.$this->quotePhpVar($name).', '.$this->quotePhpVar($value).');';
                    $node->setAttribute(self::PHP_CODE, $phpCode);
                }
            }
        });
    }
    
    /**
     * Search DOM document for all forms, and add to each of them
     * PHP code for the CSRF token
     * @param \DOMDocument $dom
     */
    protected function injectCsrfIntoDom(\DOMDocument $dom) : void
    {
        $this->walkElements($dom, function (\DOMElement $node) {
            if ($node->nodeName === 'form') {
                
                $csrf = $node->ownerDocument->createElement('input');
                $csrf->setAttribute('type', 'hidden');
                $csrf->setAttribute('name', self::PHP_CODE.'="echo htmlentities($this->getCsrfKey());"');
                $csrf->setAttribute('value', self::PHP_CODE.'="echo htmlentities($this->getCsrfValue());"');
                $csrf = $node->insertBefore($csrf, $node->firstChild);
                
                $node->insertBefore($node->ownerDocument->createTextNode("\n"), $csrf);
            }
        });
    }
    
    /**
     * Search the DOM document a node with the "form-errors" attribute, and add
     * PHP code so that the form error messages will be displayed in this node
     * @param \DOMDocument $dom
     */
    protected function injectCodeAroundFormErrors(\DOMDocument $dom) : void
    {
        $this->walkElements($dom, function (\DOMElement $node) {
            if ($node->hasAttribute('form-errors')) {
                
                $node->removeAttribute('form-errors');
                
                $codeBefore = self::PHP_CODE.'="if ($this->hasErrors()):"';
                $nodeBefore = $node->ownerDocument->createTextNode($codeBefore);
                $node->parentNode->insertBefore($nodeBefore, $node);
                
                $codeAfter = self::PHP_CODE.'="endif;"';
                $nodeAfter = $node->ownerDocument->createTextNode($codeAfter);
                $node->parentNode->insertBefore($nodeAfter, $node->nextSibling);
                
                $codeInside = self::PHP_CODE.'="echo nl2br(htmlentities($this->getErrorsAsString()));"';
                $nodeInside = $node->ownerDocument->createTextNode($codeInside);
                $node->appendChild($nodeInside);
                
            }
        });
    }
    
    /**
     * Convert the PHP code injected in the DOM, via the "inject*" methods, to real PHP code
     * @param string $html Initial HTML
     * @return string Modified HTML containing PHP code
     */
    protected function replacePhpCodeInHtml(string $html) : string
    {
        $html = preg_replace(
            '/'.preg_quote(self::PHP_CODE.'=&quot;').'(.*)'.preg_quote('&quot;').'/U', 
            '<?php \\1 ?>',
            $html);
        $html = preg_replace(
            '/'.preg_quote(self::PHP_CODE.'="').'(.*)'.preg_quote('"').'/U',
            '<?php \\1 ?>',
            $html);
        $html = preg_replace('/<\?php (.*)\$this-&gt;(.*)\?>/U', '<?php \\1$this->\\2 ?>', $html);
        return $html;
    }
    
    /**
     * Add quotes and escape special characters in a PHP variable
     * @param string $varName
     * @return string
     */
    protected function quotePhpVar(string $varName) : string
    {
        return '\'' . str_replace('\'', '\\\'', $varName) . '\'';
    }
    
    /**
     * Modify the HTML to be HTML5 compliant
     * @param string $html
     * @return string
     */
    protected function convertToHtml5(string $html) : string
    {
        $html = str_replace('required=""', 'required', $html);
        
        return $html;
    }
    
}