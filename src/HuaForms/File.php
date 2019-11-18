<?php

namespace HuaForms;

/**
 * Class representing one uploaded file
 */
class File
{
    /**
     * The original name of the file on the client machine.
     * @var string
     */
    public $name;
    
    /**
     * The mime type of the file, if the browser provided this information. An example would be "image/gif".
     * @var string
     */
    public $type;
    
    /**
     * The size, in bytes, of the uploaded file.
     * @var int
     */
    public $size;
    
    /**
     * The temporary filename of the file in which the uploaded file was stored on the server.
     * @var string
     */
    public $tmp_name;
    
    /**
     * The error code associated with this file upload.
     * @var int
     * @see https://www.php.net/manual/en/features.file-upload.errors.php
     */
    public $error;
    
    /**
     * File constructor
     * @param array $file Array representing one file, formatted like an entry of $_FILES
     * @throws \InvalidArgumentException
     */
    public function __construct(array $file)
    {
        $this->name = $file['name'] ?? '';
        $this->type = $file['type'] ?? '';
        $this->size = (int) ($file['size'] ?? 0);
        $this->tmp_name = $file['tmp_name'] ?? '';
        if (isset($file['error'])) {
            $this->error = (int) $file['error'];
        } else {
            throw new \InvalidArgumentException('Uploaded file has no "error" attribute');
        }
        if (defined('UNIT_TESTING')) {
            if ($this->error === UPLOAD_ERR_OK && !file_exists($this->tmp_name)) {
                $this->error = UPLOAD_ERR_NO_FILE;
            }
        } else {
            if ($this->error === UPLOAD_ERR_OK && !is_uploaded_file($this->tmp_name)) {
                $this->error = UPLOAD_ERR_NO_FILE;
            }
        }
    }
    
    /**
     * Return true if the file has been submitted
     * @return bool
     */
    public function isUploaded() : bool
    {
        return $this->error !== UPLOAD_ERR_NO_FILE;
    }
    
    /**
     * Return true if there was an error with the file uploading
     * @return bool
     */
    public function hasError() : bool
    {
        return $this->error !== UPLOAD_ERR_OK && $this->error !== UPLOAD_ERR_NO_FILE;
    }
    
    /**
     * Save the uploaded file on disk
     * @param string $fileName
     * @param bool $deleteTmpFile If true, the temporary file will be deleted
     * @throws \RuntimeException
     * @return bool
     */
    public function saveTo(string $fileName, bool $deleteTmpFile=true) : bool
    {
        if ($this->hasError()) {
            return false;
        }
        if (!file_exists($this->tmp_name)) {
            throw new \RuntimeException('File not found: '.$this->tmp_name.' (already moved?)');
        }
        if (!is_writable($fileName)) {
            throw new \RuntimeException('File not writable: '.$fileName);
        }
        if (defined('UNIT_TESTING')) {
            // (c) Volkswagen
            if ($deleteTmpFile) {
                return rename($this->tmp_name, $this->tmp_name);
            } else {
                return copy($this->tmp_name, $this->tmp_name);
            }
        } else {
            if ($deleteTmpFile) {
                return move_uploaded_file($this->tmp_name, $this->tmp_name);
            } else {
                if (is_uploaded_file($this->tmp_name)) {
                    return copy($this->tmp_name, $this->tmp_name);
                } else {
                    return false;
                }
            }
        }
    }
    
}