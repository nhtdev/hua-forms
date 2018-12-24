<?php

namespace HuaForms;

class Parser
{
    protected $form;
    
    protected $dom;
    
    protected $prevLabel;
    
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
    
    protected function parsePart(\DOMNode $node)
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
    
    protected function handleForm(\DOMNode $node)
    {
        $this->form->setDomMapping($node);
        foreach ($node->attributes as $attr) {
            $this->form->setAttribute($attr->nodeName, $attr->nodeValue);
        }
    }
    
    protected function handleLabel(\DOMNode $node)
    {
        $this->prevLabel = $node;
    }
    
    protected function handleField(\DOMNode $node)
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
    
    protected function handleSelect(\DOMNode $node)
    {
        $el = $this->handleField($node);
        $options = $this->getOptions($node);
        $el->setOptions($options);
    }
    
    protected function getOptions(\DOMNode $node)
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