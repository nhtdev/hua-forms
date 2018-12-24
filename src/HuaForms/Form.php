<?php

namespace HuaForms;

use HuaForms\ObjAttributes;
use HuaForms\Elements\ElementFactory;
use HuaForms\Elements\Element;

class Form
{
    use ObjAttributes;
    use DomMapping;
    
    protected $elements = [];
    
    protected $layout = null;
    
    protected $values = [];
    
    protected $validators = [];
    
    protected $formatters = [];
    
    public function __construct()
    {
    }
    
    public function getMainType() : string
    {
        return 'form';
    }
    
    public function createElement(string $type, string $name) : Element
    {
        $el = ElementFactory::create($this, $type, $name);
        $this->elements[] = $el;
        return $el;
    }
    
    public function prepareElement(string $type, string $name) : Element
    {
        $el = ElementFactory::create($this, $type, $name);
        return $el;
    }
    
    public function setLayout(\DOMDocument $dom) : Form
    {
        $this->layout = $dom;
        return $this;
    }
    
    public function getLayout()
    {
        return $this->layout;
    }
    
    public function mapElements(Callable $callback)
    {
        foreach ($this->elements as $element) {
            $callback($element);
            if ($element instanceof \HuaForms\Elements\Group) {
                $element->mapElements($callback);
            }
        }
    }
    
    public function replaceDomMapping(\DOMNode $oldNode, \DOMNode $newNode)
    {
        $this->mapElements(function (Element $element) use ($oldNode, $newNode) {
            $thisNode = $element->getDomMapping();
            if ($thisNode !== null && $thisNode->isSameNode($oldNode)) {
                $element->setDomMapping($newNode);
            }
        });
    }
    
}