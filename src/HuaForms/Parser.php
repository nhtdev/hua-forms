<?php

namespace HuaForms;

use HuaForms\Elements\Element;

/**
 * Form parser : converts a HTML file to a Form object with HTML layout
 * @author x
 *
 */
class Parser
{
    /**
     * Parsed form
     * @var \HuaForms\Form
     */
    protected $form;
    
    /**
     * Parsed HTML, Form layout
     * @var \DOMDocument
     */
    protected $dom;
    
    /**
     * Previous label found in HTML, temporary variable for parsing
     * @var \DOMNode|null
     */
    private $prevLabel;
    
    /**
     * Parse the specified file and returns the built Form
     * @param string $file File name and path
     * @return \HuaForms\Form
     */
    public function parse(string $file) : \HuaForms\Form
    {
        $this->form = new Form();
        $this->dom = new \DOMDocument();
        $this->dom->loadHTMLFile($file);
        $this->form->setLayout($this->dom);
        $this->prevLabel = null;
        $this->parsePart($this->dom);
        return $this->form;
    }
    
    /**
     * Parse a Dom node
     * @param \DOMNode $node
     */
    protected function parsePart(\DOMNode $node) : void
    {
        switch ($node->nodeName) {
            case 'form':
                $this->handleForm($node);
                break;
            case 'input':
            case 'textarea':
            case 'button':
                $this->handleField($node);
                break;
            case 'select':
                $this->handleSelect($node);
                return; // Do not parse childnodes
            case 'label':
                $this->handleLabel($node);
                break;
        }
        
        if ($node->hasChildNodes()) {
            foreach ($node->childNodes as $childNode) {
                $this->parsePart($childNode);
            }
        }
    }
    
    /**
     * Parse a <form> node
     * @param \DOMNode $node
     */
    protected function handleForm(\DOMNode $node) : void
    {
        $this->form->setDomMapping($node);
        foreach ($node->attributes as $attr) {
            $this->form->setAttribute($attr->nodeName, $attr->nodeValue);
        }
    }
    
    /**
     * Parse a <label> node
     * @param \DOMNode $node
     */
    protected function handleLabel(\DOMNode $node) : void
    {
        $this->prevLabel = $node;
    }
    
    /**
     * Parse a field node (<input>, <select>, <textarea>...)
     * @param \DOMNode $node
     * @throws ParsingException
     * @return \HuaForms\Elements\Element Parsed element
     */
    protected function handleField(\DOMNode $node) : Element
    {
        $attr = $node->attributes->getNamedItem('type');
        if ($attr === null) {
            $type = $node->nodeName;
        } else {
            $type = $attr->nodeValue;
        }
        
        $attr = $node->attributes->getNamedItem('name');
        if ($attr === null) {
            throw new ParsingException('Element "'.$node->nodeName.'" : missing "name" attribute');
        }
        $name = $attr->nodeValue;
        
        $el = $this->form->createElement($type, $name);
        foreach ($node->attributes as $attr) {
            if ($attr->nodeName !== 'type' && $attr->nodeName !== 'name') {
                $el->setAttribute($attr->nodeName, $attr->nodeValue);
            }
        }
        $el->setDomMapping($node);
        
        if ($this->prevLabel !== null && !$el->hasAttribute('label')) {
            $el->setAttribute('label', $this->prevLabel->nodeValue);
            $el->setDomMappingLabel($this->prevLabel);
        }
        
        $this->prevLabel = null;
        
        return $el;
    }
    
    /**
     * Parse a <select> node
     * @param \DOMNode $node
     */
    protected function handleSelect(\DOMNode $node) : void
    {
        $el = $this->handleField($node);
        $options = $this->getOptions($node);
        $el->setOptions($options);
    }
    
    /**
     * Parse the <option> or <optgroup> of a <select> node
     * @param \DOMNode $node
     * @return array
     */
    protected function getOptions(\DOMNode $node) : array
    {
        $options = [];
        if ($node->hasChildNodes()) {
            foreach ($node->childNodes as $childNode) {
                if ($childNode->nodeName === 'option') {
                    $attr = $childNode->attributes->getNamedItem('value');
                    if ($attr === null) {
                        $val = $childNode->nodeValue;
                    } else {
                        $val = $attr->nodeValue;
                    }
                    $label = $childNode->nodeValue;
                    $options[$val] = $label;
                } else if ($childNode->nodeName === 'optgroup') {
                    $attr = $childNode->attributes->getNamedItem('label');
                    if ($attr === null) {
                        $label = '';
                    } else {
                        $label = $attr->nodeValue;
                    }
                    if (isset($options[$label])) {
                        // When many optgroup have same label, merge them
                        $options[$label] = array_merge($options[$label], $this->getOptions($childNode));
                    } else {
                        $options[$label] = $this->getOptions($childNode);
                    }
                }
            }
        }
        return $options;
    }
    
}