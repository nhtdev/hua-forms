<?php

namespace HuaForms;

/**
 * Formatters : functions called on field values in order to modify the value (trim, uppercase, ...)
 *
 */
class Formatter
{
    
    /**
     * Apply one formatter on the given value
     * @param array $format Formatter type and options
     * @param mixed $value Initial value
     * @throws \InvalidArgumentException
     * @return mixed Modified value
     */
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
    
    /**
     * Trim : Strip whitespace (or other characters) from the beginning and end of a string
     * @param array $format Formatter options
     * @param mixed $value Initial value
     * @throws \InvalidArgumentException
     * @return mixed Modified value
     */
    public function formatTrim(array $format, $value)
    {
        if (!is_string($value)) {
            throw new \InvalidArgumentException('Format trim : value must be a string');
        }
        return trim($value);
    }
    
    /**
     * Number : cast value to int or float if value is a valid number
     * @param array $format Formatter options
     * @param mixed $value Initial value
     * @throws \InvalidArgumentException
     * @return mixed Modified value
     */
    public function formatNumber(array $format, $value)
    {
        if (is_array($value)) {
            throw new \InvalidArgumentException('Format number : value cannot be an array');
        }
        if (is_numeric($value)) {
            if (strpos($value, '.') === false) {
                return (int) $value;
            } else {
                return (float) $value;
            }
        } else {
            return $value;
        }
    }
    
}