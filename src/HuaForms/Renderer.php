<?php

namespace HuaForms;

use HuaForms\Elements\Element;
use HuaForms\Registry\DefaultRegistrySet;

/**
 * Form renderer : converts a form object to HTML, using its layout
 * @author x
 *
 */
class Renderer
{
    /**
     * Converts the given form to HTML, using form layout
     * @param Form $form
     * @return string
     */
    public function render(Form $form) : string
    {
        $this->runRegistryRenderers($form);
        // TODO set values
        $dom = $form->getLayout();
        return $dom->saveXML($dom->documentElement);
    }
        
    /**
     * Call all the registered "unit renderers" of the form
     * @param Form $form
     */
    protected function runRegistryRenderers(Form $form) : void
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
