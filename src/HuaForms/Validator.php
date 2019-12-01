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
        $method = 'validate'.ucfirst(str_replace('-', '', $rule['type']));
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
    protected function validateRequired(array $rule, $value) : bool
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
    protected function validateMaxlength(array $rule, $value) : bool
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
    protected function validateMinlength(array $rule, $value) : bool
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
    protected function validateInarray(array $rule, $value) : bool
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
     * upload-error : the file must not have upload error
     * @param array $rule Not used
     * @param \HuaForms\File|array $file File or array of file
     * @return bool True if value is valid, false otherwise
     */
    protected function validateUploaderror(array $rule, $file) : bool
    {
        if (is_array($file)) {
            foreach ($file as $key => $oneFile) {
                if (!$oneFile instanceof \HuaForms\File) {
                    throw new \InvalidArgumentException('Rule upload-error : given value (key '.$key.') is not a \HuaForms\File');
                }
            }
            foreach ($file as $oneFile) {
                $result = $this->validateUploaderror($rule, $oneFile);
                if ($result !== true) {
                    return $result;
                }
            }
            return true;
        }
        if (!$file instanceof \HuaForms\File) {
            throw new \InvalidArgumentException('Rule upload-error : given value is not a \HuaForms\File');
        }
        if (!$file->isUploaded() || $file->hasError()) {
            return false;
        }
        return true;
    }
    
    /**
     * accept : the file must match the specified format (extension or mime type)
     * @param array $rule ['formats' => ['.pdf', 'image/*', 'text/plain', ...] ]
     * @param \HuaForms\File|array $file File or array of file
     * @return bool True if value is valid, false otherwise
     */
    protected function validateAccept(array $rule, $file) : bool
    {
        if (!isset($rule['formats'])) {
            throw new \InvalidArgumentException('Rule accept : missing param "formats"');
        }
        if (!is_array($rule['formats'])) {
            throw new \InvalidArgumentException('Rule accept : param "formats" is not an array');
        }
        if (is_array($file)) {
            foreach ($file as $oneFile) {
                $result = $this->validateAccept($rule, $oneFile);
                if ($result !== true) {
                    return $result;
                }
            }
            return true;
        }
        if (!$file instanceof \HuaForms\File) {
            throw new \InvalidArgumentException('Rule accept : given value is not a \HuaForms\File');
        }
        if (!$file->isUploaded() || $file->hasError()) {
            return true; // validateUploaderror has already sent an error message
        }
        $extension = $file->getExtension();
        foreach ($rule['formats'] as $oneFormat) {
            if (substr($oneFormat, 0, 1) === '.') {
                // Ex : .pdf
                if (strcasecmp(substr($oneFormat, 1), $extension) === 0) {
                    return true;
                }
            } else if ($oneFormat === 'audio/*' || $oneFormat === 'video/*' || $oneFormat == 'image/*') {
                if ($file->typeServerSide !== false) {
                    $regex = '/^'.str_replace(['*', '/'], ['.*', '\\/'], $oneFormat) . '$/';
                    if (preg_match($regex, $file->typeServerSide)) {
                        return true;
                    }
                }
            } else {
                // Mime type
                if ($file->typeServerSide !== false && $oneFormat === $file->typeServerSide) {
                    return true;
                }
            }
        }
        return false;
    }
    
    /**
     * Email : the string value must be a valid email
     * @param array $rule Rule options (min, max, step)
     * @param mixed $value Field value
     * @return bool True if value is valid, false otherwise
     */
    protected function validateEmail(array $rule, $value) : bool
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
    protected function validateUrl(array $rule, $value) : bool
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
    protected function validateColor(array $rule, $value) : bool
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
    protected function validateNumber(array $rule, $value)
    {
        if (is_array($value)) {
            throw new \InvalidArgumentException('Rule number : value cannot be an array');
        }
        if (preg_match('/^\-?\d+\.?\d*$/', $value)) {
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
                $rest = abs(fmod((float) $valtmp, (float) $step));
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
    protected function validateMonth(array $rule, $value)
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
            if (isset($rule['min']) && strcmp($value, $rule['min']) < 0) {
                return 'min';
            }
            if (isset($rule['max']) && strcmp($value, $rule['max']) > 0) {
                return 'max';
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
    protected function validateWeek(array $rule, $value)
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
            if (isset($rule['min']) && strcmp($value, $rule['min']) < 0) {
                return 'min';
            }
            if (isset($rule['max']) && strcmp($value, $rule['max']) > 0) {
                return 'max';
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
    protected function validateDate(array $rule, $value)
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
            if (isset($rule['min']) && strcmp($value, $rule['min']) < 0) {
                return 'min';
            }
            if (isset($rule['max']) && strcmp($value, $rule['max']) > 0) {
                return 'max';
            }
            return true;
            
        } else {
            return false;
        }
        
    }
    
    /**
     * Time : the string value must be a valid date "yyyy-mm-dd"
     * @param array $rule Not used
     * @param mixed $value Field value
     * @return mixed True if value is valid, false or string otherwise
     */
    protected function validateTime(array $rule, $value)
    {
        if (is_array($value)) {
            throw new \InvalidArgumentException('Rule time : value cannot be an array');
        }
        
        if (isset($rule['step'])) {
            $step = (int) $rule['step'];
            if ($step < 1) {
                $step = 60;
            }
        } else {
            $step = 60;
        }
        
        if ($step < 60) {
            if (preg_match('/^(\d\d):(\d\d):(\d\d)$/', $value, $matches)) {
                $hour = (int) $matches[1];
                $minute = (int) $matches[2];
                $second = (int) $matches[3];
            } else {
                return false;
            }
        } else {
            if (preg_match('/^(\d\d):(\d\d)$/', $value, $matches)) {
                $hour = (int) $matches[1];
                $minute = (int) $matches[2];
                $second = 0;
            } else {
                return false;
            }
        }
        
        if ($hour < 0 || $hour > 23) {
            return false;
        }
        if ($minute < 0 || $minute > 59) {
            return false;
        }
        if ($second !== null && ($second < 0 || $second > 59)) {
            return false;
        }
        
        if (isset($rule['min']) && isset($rule['max']) && strcmp($rule['min'], $rule['max']) > 0) {
            // Inverse (min > max)
            if (strcmp($value, $rule['min']) < 0 && strcmp($value, $rule['max']) > 0) {
                return 'inverse';
            }
        } else {
            if (isset($rule['min']) && strcmp($value, $rule['min']) < 0) {
                return 'min';
            }
            if (isset($rule['max']) && strcmp($value, $rule['max']) > 0) {
                return 'max';
            }
        }
        
        if (isset($rule['step']) && $rule['step'] > 0) {
            $totalSeconds = $second + 60*$minute + 60*60*$hour;
            $start = 0;
            if (isset($rule['min'])) {
                if (preg_match('/^(\d\d):(\d\d):(\d\d)$/', $rule['min'], $matches)) {
                    $minHour = (int) $matches[1];
                    $minMinute = (int) $matches[2];
                    $minSecond = (int) $matches[3];
                    $start = $minSecond + 60*$minMinute + 60*60*$minHour;
                } else if (preg_match('/^(\d\d):(\d\d)$/', $rule['min'], $matches)) {
                    $minHour = (int) $matches[1];
                    $minMinute = (int) $matches[2];
                    $start = 60*$minMinute + 60*60*$minHour;
                }
            }
            if ( abs($totalSeconds-$start) % $rule['step'] !== 0) {
                return 'step';
            }
        }
        
        return true;
        
    }
    
    /**
     * Datetimelocal : the string value must be a valid date "yyyy-mm-ddThh:mm"
     * @param array $rule Not used
     * @param mixed $value Field value
     * @return mixed True if value is valid, false or string otherwise
     */
    protected function validateDatetimelocal(array $rule, $value)
    {
        if (is_array($value)) {
            throw new \InvalidArgumentException('Rule datetime-local : value cannot be an array');
        }
        
        if (isset($rule['step'])) {
            $step = (int) $rule['step'];
            if ($step < 1) {
                $step = 60;
            }
        } else {
            $step = 60;
        }
        
        if ($step < 60) {
            if (preg_match('/^(\d\d\d\d)-(\d\d)-(\d\d)T(\d\d):(\d\d):(\d\d)$/', $value, $matches)) {
                $year = (int) $matches[1];
                $month = (int) $matches[2];
                $day = (int) $matches[3];
                $hour = (int) $matches[4];
                $minute = (int) $matches[5];
                $second = (int) $matches[6];
            } else {
                return false;
            }
        } else {
            if (preg_match('/^(\d\d\d\d)-(\d\d)-(\d\d)T(\d\d):(\d\d)$/', $value, $matches)) {
                $year = (int) $matches[1];
                $month = (int) $matches[2];
                $day = (int) $matches[3];
                $hour = (int) $matches[4];
                $minute = (int) $matches[5];
                $second = 0;
            } else {
                return false;
            }
        }
        
        if (!checkdate($month, $day, $year)) {
            return false;
        }
        if ($hour < 0 || $hour > 23) {
            return false;
        }
        if ($minute < 0 || $minute > 59) {
            return false;
        }
        if ($second !== null && ($second < 0 || $second > 59)) {
            return false;
        }
        
        if (isset($rule['min']) && strcmp($value, $rule['min']) < 0) {
            return 'min';
        }
        if (isset($rule['max']) && strcmp($value, $rule['max']) > 0) {
            return 'max';
        }
        
        if (isset($rule['step']) && $rule['step'] > 0 && $rule['step'] < 86400) {
            $totalSeconds = $second + 60*$minute + 60*60*$hour;
            $start = 0;
            if (isset($rule['min'])) {
                if (preg_match('/^\d\d\d\d-\d\d-\d\dT(\d\d):(\d\d):(\d\d)$/', $rule['min'], $matches)) {
                    $minHour = (int) $matches[1];
                    $minMinute = (int) $matches[2];
                    $minSecond = (int) $matches[3];
                    $start = $minSecond + 60*$minMinute + 60*60*$minHour;
                } else if (preg_match('/^\d\d\d\d-\d\d-\d\dT(\d\d):(\d\d)$/', $rule['min'], $matches)) {
                    $minHour = (int) $matches[1];
                    $minMinute = (int) $matches[2];
                    $start = 60*$minMinute + 60*60*$minHour;
                }
            }
            if ( abs($totalSeconds-$start) % $rule['step'] !== 0) {
                return 'step';
            }
        }
        
        return true;
        
    }
    
    /**
     * Pattern : the string value must match the given pattern
     * @param array $rule ['pattern' => regex]
     * @param mixed $value Field value
     * @return bool True if value is valid, false otherwise
     */
    protected function validatePattern(array $rule, $value) : bool
    {
        if (!is_string($value)) {
            throw new \InvalidArgumentException('Rule pattern : value must be a string');
        }
        if (!isset($rule['pattern'])) {
            throw new \InvalidArgumentException('Rule pattern : missing param "pattern"');
        }
        if (preg_match('/^'.$rule['pattern'].'$/', $value)) {
            return true;
        } else {
            return false;
        }
    }
    
}