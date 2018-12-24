<?php

namespace HuaForms;

use HuaForms\Elements\Element;
use HuaForms\Registry\DefaultRegistrySet;

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
        $registrySet = DefaultRegistrySet::get();
        
        $renderers = $registrySet->registryRenderCreate->get('form');
        foreach ($renderers as $renderer) {
            $renderer->process($form);
        }
        
        $form->mapElements(function (Element $element) use ($registrySet) {
            
            $labelRenderers = $registrySet->registryRenderCreate->get('label');
            foreach ($labelRenderers as $rendererClass) {
                $renderer = new $rendererClass();
                $renderer->process($element);
            }
            
            $type = $element->getMainType();
            $renderersCreate = $registrySet->registryRenderCreate->get($type);
            foreach ($renderersCreate as $rendererClass) {
                $renderer = new $rendererClass();
                $renderer->process($element);
            }
            
        });
            
        $form->mapElements(function (Element $element) use ($registrySet) {
            
            $type = $element->getMainType();
            $renderersUpdate = $registrySet->registryRenderUpdate->get($type);
            foreach ($renderersUpdate as $rendererClass) {
                $renderer = new $rendererClass();
                $renderer->process($element);
            }
            
        });
        
    }
    
}
