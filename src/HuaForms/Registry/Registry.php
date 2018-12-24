<?php

namespace HuaForms\Registry;

class Registry
{
    
    protected $registry = [];
    
    public function register(
        string $elementType,
        string $attrName,
        $attrValue,             // *
        $builderClass,
        $rendererClass) : Registry
    {
        if ($builderClass !== null) {
            $this->registry[$elementType][$attrName][$attrValue]['builder'][] = $builderClass;
        }
        if ($rendererClass !== null) {
            $this->registry[$elementType][$attrName][$attrValue]['renderer'][] = $rendererClass;
        }
        return $this;
    }
    
    public function getBuilders(string $elementType, string $attrName, $attrValue) : array
    {
        return $this->getAny('builder', $elementType, $attrName, $attrValue);
    }
    
    public function getRenderers(string $elementType, string $attrName, $attrValue) : array
    {
        return $this->getAny('renderer', $elementType, $attrName, $attrValue);
    }
    
    protected function getAny(string $type, string $elementType, string $attrName, $attrValue) : array
    {
        $result = [];
        if (isset($this->registry[$elementType][$attrName]['*'][$type])) {
            $result = $this->registry[$elementType][$attrName]['*'][$type];
        }
        if ($attrValue !== '*' && isset($this->registry[$elementType][$attrName][$attrValue][$type])) {
            $result = array_merge($result, $this->registry[$elementType][$attrName][$attrValue][$type]);
        }
        return $result;
    }
    
}