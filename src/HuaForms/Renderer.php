<?php

namespace HuaForms;

use HuaForms\Elements\Element;
use HuaForms\Registry\DefaultRegistry;

class Renderer
{
    protected $form;
    
    public function render(Form $form) : string
    {
        $this->runRegistryRenderers($form);
        // TODO set values
        return $form->getLayout()->saveXML();
    }
        
    protected function runRegistryRenderers(Form $form)
    {
        $registry = DefaultRegistry::get();
        
        foreach ($form->getAttributes() as $attrName => $attrValue) {
            $renderers = $registry->getRenderers('form', $attrName, $attrValue);
            foreach ($renderers as $renderer) {
                $renderer->render($form);
            }
        }
        
        $labelRenderers = $registry->getRenderers('label', '*', '*');
        $form->mapElements(function (Element $element) use ($registry, $labelRenderers) {
            
            foreach ($labelRenderers as $rendererClass) {
                $renderer = new $rendererClass();
                $renderer->render($element);
            }
            
            $type = $element->getMainType();
            foreach ($element->getAttributes() as $attrName => $attrValue) {
                $renderers = $registry->getRenderers($type, $attrName, $attrValue);
                foreach ($renderers as $rendererClass) {
                    $renderer = new $rendererClass();
                    $renderer->render($element);
                }
            }
        });
        
    }
    
}
