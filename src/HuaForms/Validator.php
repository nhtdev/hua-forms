<?php

namespace HuaForms;

/**
 * validators : function called to check if a field value is valid (required, length, ...)
 *
 */
class Validator
{
    
    /**
     * Test one validation rule against the given value
     * @param array $rule Rule type and options
     * @param mixed $value Field value
     * @throws \InvalidArgumentException
     * @return mixed True if value is valid, false or string otherwise
     */
    public function validate(array $rule, $value)
    {
        if (empty($rule['type'])) {
            throw new \InvalidArgumentException('Rule type is empty');
        }
        $method = 'validate'.ucfirst($rule['type']);
        if (!method_exists($this, $method)) {
            throw new \InvalidArgumentException('Invalid rule type "'.$rule['type'].'"');
        }
        return $this->$method($rule, $value);
    }
    
    /**
     * Required : the value must not be empty
     * @param array $rule Not used
     * @param mixed $value Field value
     * @return bool True if value is valid, false otherwise
     */
    public function validateRequired(array $rule, $value) : bool
    {
        if (empty($value) && $value !== '0') {
            return false;
        } else {
            return true;
        }
    }
    
    /**
     * MaxLength : the string value must have at least n characters
     * @param array $rule ['maxlength' => n]
     * @param mixed $value Field value
     * @return bool True if value is valid, false otherwise
     */
    public function validateMaxlength(array $rule, $value) : bool
    {
        if (!is_string($value)) {
            throw new \InvalidArgumentException('Rule maxlength : value must be a string');
        }
        if (!isset($rule['maxlength'])) {
            throw new \InvalidArgumentException('Rule maxlength : missing param "maxlength"');
        }
        if (mb_strlen($value) > $rule['maxlength']) {
            return false;
        } else {
            return true;
        }
    }
    
    /**
     * MinLength : the string value must have at most n characters
     * @param array $rule ['minlength' => n]
     * @param mixed $value Field value
     * @return bool True if value is valid, false otherwise
     */
    public function validateMinlength(array $rule, $value) : bool
    {
        if (!is_string($value)) {
            throw new \InvalidArgumentException('Rule minlength : value must be a string');
        }
        if (!isset($rule['minlength'])) {
            throw new \InvalidArgumentException('Rule minlength : missing param "minlength"');
        }
        if (mb_strlen($value) < $rule['minlength']) {
            return false;
        } else {
            return true;
        }
    }
    
    /**
     * inarray : the value(s) are limited to a given list of values
     * @param array $rule ['values' => [array of allowed values] ]
     * @param mixed $value Field value
     * @return bool True if value is valid, false otherwise
     */
    public function validateInarray(array $rule, $value) : bool
    {
        if (!isset($rule['values'])) {
            throw new \InvalidArgumentException('Rule maxlength : missing param "values"');
        }
        $values = $rule['values'];
        if (!is_array($values)) {
            throw new \InvalidArgumentException('Rule maxlength : param "values" must be an array');
        }
        if (is_array($value)) {
            foreach ($value as $oneValue) {
                if (!in_array($oneValue, $values)) {
                    return false;
                }
            }
            return true;
        } else {
            return in_array($value, $values);
        }
    }
    
    /**
     * Email : the string value must be a valid email
     * @param array $rule Rule options (min, max, step)
     * @param mixed $value Field value
     * @return bool True if value is valid, false otherwise
     */
    public function validateEmail(array $rule, $value) : bool
    {
        if (!is_string($value)) {
            throw new \InvalidArgumentException('Rule email : value must be a string');
        }
        if (filter_var($value, FILTER_VALIDATE_EMAIL)) {
            return true;
        } else {
            return false;
        }
    }
    
    /**
     * Url : the string value must be a valid url
     * @param array $rule Not used
     * @param mixed $value Field value
     * @return bool True if value is valid, false otherwise
     */
    public function validateUrl(array $rule, $value) : bool
    {
        if (!is_string($value)) {
            throw new \InvalidArgumentException('Rule url : value must be a string');
        }
        if (filter_var($value, FILTER_VALIDATE_URL)) {
            return true;
        } else {
            return false;
        }
    }
    
    /**
     * Color : the string value must be a valid color #12345ab
     * @param array $rule Not used
     * @param mixed $value Field value
     * @return bool True if value is valid, false otherwise
     */
    public function validateColor(array $rule, $value) : bool
    {
        if (!is_string($value)) {
            throw new \InvalidArgumentException('Rule color : value must be a string');
        }
        if (preg_match('/^#[0-9a-f]{6}$/', $value)) {
            return true;
        } else {
            return false;
        }
    }
    
    /**
     * Number : the string value must be a valid number
     * @param array $rule Not used
     * @param mixed $value Field value
     * @return mixed True if value is valid, false or string otherwise
     */
    public function validateNumber(array $rule, $value)
    {
        if (is_array($value)) {
            throw new \InvalidArgumentException('Rule number : value cannot be an array');
        }
        if (is_numeric($value)) {
            if (isset($rule['min'])) {
                if ($value < $rule['min']) {
                    return 'min'; // Erreur
                }
            }
            if (isset($rule['max'])) {
                if ($value > $rule['max']) {
                    return 'max'; // Erreur
                }
            }
            if (isset($rule['step'])) {
                $step = $rule['step'];
            } else {
                $step = 1;
            }
            if ($step !== 'any' && $step > 0) {
                if (isset($rule['min'])) {
                    $min = $rule['min'];
                } else {
                    $min = 0;
                }
                $valtmp = $value - $min; 
                $rest = fmod((float) $valtmp, (float) $step);
                if ($rest > 0.0000000001 && $rest < $step - 0.0000000001) { // Pour les erreurs d'arrondi PHP
                    return 'step'; // Erreur
                }
            }
            return true;
        } else {
            return false;
        }
    }
    
    
    /**
     * Month : the string value must be a valid year + month "yyyy-mm"
     * @param array $rule Not used
     * @param mixed $value Field value
     * @return mixed True if value is valid, false or string otherwise
     */
    public function validateMonth(array $rule, $value)
    {
        if (is_array($value)) {
            throw new \InvalidArgumentException('Rule month : value cannot be an array');
        }
        
        if (preg_match('/^(\d\d\d\d)-(\d\d)$/', $value, $matches)) {
            $year = (int) $matches[1];
            $month = (int) $matches[2];
            if ($month < 1 || $month > 12) {
                return false;
            }
            
            if (isset($rule['min'])) {
                if (preg_match('/^(\d\d\d\d)-(\d\d)$/', $rule['min'], $ruleMatches)) {
                    $ruleYear = (int) $ruleMatches[1];
                    $ruleMonth = (int) $ruleMatches[2];
                    if ($year < $ruleYear) {
                        return 'min';
                    }
                    if ($year === $ruleYear && $month < $ruleMonth) {
                        return 'min';
                    }
                }
            }
            
            if (isset($rule['max'])) {
                if (preg_match('/^(\d\d\d\d)-(\d\d)$/', $rule['max'], $ruleMatches)) {
                    $ruleYear = (int) $ruleMatches[1];
                    $ruleMonth = (int) $ruleMatches[2];
                    if ($year > $ruleYear) {
                        return 'max';
                    }
                    if ($year === $ruleYear && $month > $ruleMonth) {
                        return 'max';
                    }
                }
            }
            return true;
            
        } else {
            return false;
        }
        
    }
    
    /**
     * Week : the string value must be a valid year + week "yyyy-Www"
     * @param array $rule Not used
     * @param mixed $value Field value
     * @return mixed True if value is valid, false or string otherwise
     */
    public function validateWeek(array $rule, $value)
    {
        if (is_array($value)) {
            throw new \InvalidArgumentException('Rule week : value cannot be an array');
        }
        
        if (preg_match('/^(\d\d\d\d)-W(\d\d)$/', $value, $matches)) {
            $year = (int) $matches[1];
            $week = (int) $matches[2];
            $weekCount = date('W', strtotime($year . '-12-28'));
            if ($week < 1 || $week > $weekCount) {
                return false;
            }
            
            if (isset($rule['min'])) {
                if (preg_match('/^(\d\d\d\d)-W(\d\d)$/', $rule['min'], $ruleMatches)) {
                    $ruleYear = (int) $ruleMatches[1];
                    $ruleWeek = (int) $ruleMatches[2];
                    if ($year < $ruleYear) {
                        return 'min';
                    }
                    if ($year === $ruleYear && $week < $ruleWeek) {
                        return 'min';
                    }
                }
            }
            
            if (isset($rule['max'])) {
                if (preg_match('/^(\d\d\d\d)-W(\d\d)$/', $rule['max'], $ruleMatches)) {
                    $ruleYear = (int) $ruleMatches[1];
                    $ruleWeek = (int) $ruleMatches[2];
                    if ($year > $ruleYear) {
                        return 'max';
                    }
                    if ($year === $ruleYear && $week > $ruleWeek) {
                        return 'max';
                    }
                }
            }
            return true;
            
        } else {
            return false;
        }
        
    }
    
    /**
     * Date : the string value must be a valid date "yyyy-mm-dd"
     * @param array $rule Not used
     * @param mixed $value Field value
     * @return mixed True if value is valid, false or string otherwise
     */
    public function validateDate(array $rule, $value)
    {
        if (is_array($value)) {
            throw new \InvalidArgumentException('Rule date : value cannot be an array');
        }
        
        if (preg_match('/^(\d\d\d\d)-(\d\d)-(\d\d)$/', $value, $matches)) {
            $year = (int) $matches[1];
            $month = (int) $matches[2];
            $day = (int) $matches[3];
            if (!checkdate($month, $day, $year)) {
                return false;
            }
            
            if (isset($rule['min'])) {
                if (preg_match('/^(\d\d\d\d)-(\d\d)-(\d\d)$/', $rule['min'], $ruleMatches)) {
                    $ruleYear = (int) $ruleMatches[1];
                    $ruleMonth = (int) $ruleMatches[2];
                    $ruleDay = (int) $ruleMatches[3];
                    if ($year < $ruleYear) {
                        return 'min';
                    }
                    if ($year === $ruleYear && $month < $ruleMonth) {
                        return 'min';
                    }
                    if ($year === $ruleYear && $month === $ruleMonth && $day < $ruleDay) {
                        return 'min';
                    }
                }
            }
            
            if (isset($rule['max'])) {
                if (preg_match('/^(\d\d\d\d)-(\d\d)-(\d\d)$/', $rule['max'], $ruleMatches)) {
                    $ruleYear = (int) $ruleMatches[1];
                    $ruleMonth = (int) $ruleMatches[2];
                    $ruleDay = (int) $ruleMatches[3];
                    if ($year > $ruleYear) {
                        return 'max';
                    }
                    if ($year === $ruleYear && $month > $ruleMonth) {
                        return 'max';
                    }
                    if ($year === $ruleYear && $month === $ruleMonth && $day > $ruleDay) {
                        return 'max';
                    }
                }
            }
            return true;
            
        } else {
            return false;
        }
        
    }
    
}