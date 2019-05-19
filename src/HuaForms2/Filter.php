<?php

namespace HuaForms2;

class Filter
{
    
    public function filter(array $filter, $value)
    {
        if (empty($filter['type'])) {
            throw new \InvalidArgumentException('Filter type is empty');
        }
        $method = 'filter'.ucfirst($filter['type']);
        if (!method_exists($this, $method)) {
            throw new \InvalidArgumentException('Invalid filter type "'.$filter['type'].'"');
        }
        return $this->$method($filter, $value);
    }
    
    public function filterTrim(array $filter, $value)
    {
        if (!is_string($value)) {
            throw new \InvalidArgumentException('Filter trim : value must be a string');
        }
        return trim($value);
    }
    
}