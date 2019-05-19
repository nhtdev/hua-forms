<?php

namespace HuaForms2;

class Formatter
{
    
    public function format(array $format, $value)
    {
        if (empty($format['type'])) {
            throw new \InvalidArgumentException('Format type is empty');
        }
        $method = 'format'.ucfirst($format['type']);
        if (!method_exists($this, $method)) {
            throw new \InvalidArgumentException('Invalid format type "'.$format['type'].'"');
        }
        return $this->$method($format, $value);
    }
    
    public function formatTrim(array $format, $value)
    {
        if (!is_string($value)) {
            throw new \InvalidArgumentException('Format trim : value must be a string');
        }
        return trim($value);
    }
    
}