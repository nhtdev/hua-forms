<?php

namespace HuaForms2;

class Parser
{
    protected $inputFile;
    
    protected $submitCpt = 1;
    
    const PHP_CODE = 'PHP_CODE';
    
    public function __construct(string $inputFile)
    {
        $this->inputFile = $inputFile;
    }
    
    public function parse(string $outputPhp, string $outputJson) : void
    {
        // 1- Parse DOM HTML
        $dom = new \DOMDocument();
        $dom->loadHTMLFile($this->inputFile, LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD);

        // 2- Modify DOM HTML
        $this->modifyDom($dom);
        
        // 3- Build JSON From DOM
        $data = $this->buildJsonFromDom($dom);
        
        // 4- Write JSON
        $this->writeJson($data, $outputJson);
        
        // 5- Clean DOM
        $this->cleanDom($dom);
        
        // 6- Write DOM
        $this->writeDom($dom, $outputPhp);
    }
    
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
    
    protected function modifyDom(\DOMDocument $dom) : void
    {
        $this->addIdAttributes($dom);
        $this->addAlertDivIfNotFound($dom);
    }
    
    protected function addIdAttributes(\DOMDocument $dom) : void
    {
        // TODO
    }
    
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
                if ($node->nodeName === 'form') {
                    
                    $div = $node->ownerDocument->createElement('div');
                    $div->setAttribute('form-errors', 'true');
                    $node->insertBefore($div, $node->firstChild);
                    
                    $node->insertBefore($node->ownerDocument->createTextNode("\n"), $div);
                }
            });
        }
    }
    
    protected function buildJsonFromDom(\DOMDocument $dom) : array
    {
        $data = [
            'method' => '',
            'fields' => [],
            'submits' => []
        ];
        
        $this->walkElements($dom, function (\DOMElement $node) use (&$data) {
            switch ($node->nodeName) {
                
                case 'form':
                    $this->buildJsonForm($node, $data);
                    break;
                    
                case 'input':
                    if ($node->hasAttribute('type')) {
                        $type = $node->getAttribute('type');
                    } else {
                        $type = 'text';
                    }
                    switch ($type) {
                        case 'button':
                            $this->buildJsonButton($node, $data);
                            break;
                        case 'submit':
                            $this->buildJsonSubmit($node, $data);
                            break;
                        default:
                            $this->buildJsonInput($type, $node, $data);
                            break;
                    }
                    break;
                case 'textarea':
                    $this->buildJsonInput('textarea', $node, $data);
                    break;
                case 'select':
                    $this->buildJsonInput('select', $node, $data);
                    break;
                case 'button':
                    if ($node->hasAttribute('type')) {
                        $type = $node->getAttribute('type');
                    } else {
                        $type = 'button';
                    }
                    switch ($type) {
                        case 'submit':
                            $this->buildJsonSubmit($node, $data);
                            break;
                        case 'button':
                        default:
                            $this->buildJsonButton($node, $data);
                            break;
                    }
                    break;
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
    
    protected function buildJsonForm(\DOMElement $node, array &$data) : void
    {
        if ($node->hasAttribute('method')) {
            $data['method'] = $node->getAttribute('method');
        }
    }
    
    protected function buildJsonButton(\DOMElement $node, array &$data) : void
    {
        // Do nothing
    }
    
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
    
    protected function buildJsonInput(string $type, \DOMElement $node, array &$data) : void
    {
        // Name
        if (!$node->hasAttribute('name')) {
            // Field is not sent to server when submitted
            return;
        }
        $name = $node->getAttribute('name');
        
        // Label
        $labelNode = $this->findLabelNode($node);
        if ($labelNode === null) {
            $label = '';
        } else {
            $label = $labelNode->nodeValue;
        }
        
        // Formatters
        $formatters = [];
        
        // Rules
        $rules = [];
        
        // Save
        $data['fields'][] = [
            'label' => $label,
            'name' => $name,
            'type' => $type,
            'formatters' => $formatters,
            'rules' => $rules
        ];
        
    }
    
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
    
    protected function writeJson(array $data, string $file) : void
    {
        file_put_contents($file, json_encode($data));
    }
    
    protected function cleanDom(\DOMDocument $dom) : void
    {
        // TODO
    }
    
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
    
    protected function quotePhpVar(string $varName) : string
    {
        return '\'' . str_replace('\'', '\\\'', $varName) . '\'';
    }
    
    protected function convertToHtml5(string $html) : string
    {
        $html = str_replace('required=""', 'required', $html);
        
        return $html;
    }
    
}