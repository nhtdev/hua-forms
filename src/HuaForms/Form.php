<?php

namespace HuaForms;

use HuaForms\Elements\ElementFactory;
use HuaForms\Elements\Element;

/**
 * Form
 * @author x
 *
 */
class Form
{
    use ObjAttributes;
    use DomMapping;
    
    /**
     * All elements of the form : array of \HuaForms\Elements\Element
     * @var array
     */
    protected $elements = [];
    
    /**
     * Layout of the forme (HTML for rendering)
     * @var \DOMDocument
     */
    protected $layout = null;
    
    /**
     * Form values
     * @var array
     */
    protected $values = [];
    
    /**
     * Form validators : array of objets (todo)
     * @var array
     */
    protected $validators = [];
    
    /**
     * Form formatters : array of objets (todo)
     * @var array
     */
    protected $formatters = [];
    
    /**
     * Constructor
     */
    public function __construct()
    {
    }
    
    /**
     * Returns the type of this element (form)
     * @return string
     */
    public function getMainType() : string
    {
        return 'form';
    }
    
    /**
     * Creates a new element and adds it to this form
     * @param string $type Element type
     * @param string $name Element name
     * @return Element Created element
     */
    public function createElement(string $type, string $name) : Element
    {
        $el = ElementFactory::create($this, $type, $name);
        $this->elements[] = $el;
        return $el;
    }
    
    /**
     * Creates and return a new Element, but not associated to the form
     * @param string $type Element type
     * @param string $name Element name
     * @return Element Created element
     */
    public function prepareElement(string $type, string $name) : Element
    {
        $el = ElementFactory::create($this, $type, $name);
        return $el;
    }
    
    /**
     * Sets the HTML layout of the form
     * @param \DOMDocument $dom
     */
    public function setLayout(\DOMDocument $dom) : void
    {
        $this->layout = $dom;
    }
    
    /**
     * Returns the layout of the form
     * @return \DOMDocument|null
     */
    public function getLayout() : ?\DOMDocument
    {
        return $this->layout;
    }
    
    /**
     * Call a function on each one of the form elements (including elements inside a group)
     * @param callable $callback
     */
    public function mapElements(Callable $callback) : void
    {
        foreach ($this->elements as $element) {
            $callback($element);
            if ($element instanceof \HuaForms\Elements\Group) {
                $element->mapElements($callback);
            }
        }
    }
    
    /**
     * Changes the DOM Mapping of every form elements (including elements inside a group)
     * Any element pointing to $oldNode will then point to $newNode
     * @param \DOMNode $oldNode
     * @param \DOMNode $newNode
     */
    public function replaceDomMapping(\DOMNode $oldNode, \DOMNode $newNode) : void
    {
        $this->mapElements(function (Element $element) use ($oldNode, $newNode) {
            $thisNode = $element->getDomMapping();
            if ($thisNode !== null && $thisNode->isSameNode($oldNode)) {
                $element->setDomMapping($newNode);
            }
        });
    }
    
}