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
     * Do not trust this information !!
     * @var string
     */
    public $typeClientSide;
    
    /**
     * The mime type of the file, determined by the server. An example would be "image/gif".
     * False if type not found
     * @var string|bool
     */
    public $typeServerSide;
    
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
     * True in order to assert that the file is a real uploaded file, false otherwise
     * @var bool
     */
    protected $checkUploadedFile;
    
    /**
     * File constructor
     * @param array $file Array representing one file, formatted like an entry of $_FILES
     * @param bool $checkUploadedFile True (default) in order to assert that the file is a real uploaded file, false otherwise. Always use true, except for unit testing
     * @throws \InvalidArgumentException
     */
    public function __construct(array $file, bool $checkUploadedFile=true)
    {
        $this->checkUploadedFile = $checkUploadedFile;
        $this->name = $file['name'] ?? '';
        $this->typeClientSide = $file['type'] ?? '';
        $this->size = (int) ($file['size'] ?? 0);
        $this->tmp_name = $file['tmp_name'] ?? '';
        if (isset($file['error'])) {
            $this->error = (int) $file['error'];
        } else {
            throw new \InvalidArgumentException('Uploaded file has no "error" attribute');
        }
        if ($this->checkUploadedFile) {
            if ($this->error === UPLOAD_ERR_OK && !is_uploaded_file($this->tmp_name)) {
                $this->error = UPLOAD_ERR_NO_FILE;
            }
        } else {
            if ($this->error === UPLOAD_ERR_OK && !file_exists($this->tmp_name)) {
                $this->error = UPLOAD_ERR_NO_FILE;
            }
        }
        if ($this->isUploaded() && !$this->hasError()) {
            $this->typeServerSide = mime_content_type($this->tmp_name);
        } else {
            $this->typeServerSide = false;
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
        if (!$this->isUploaded()) {
            return false;
        }
        if ($this->hasError()) {
            return false;
        }
        if (!file_exists($this->tmp_name)) {
            throw new \RuntimeException('File not found: '.$this->tmp_name.' (already moved?)');
        }
        if ($this->checkUploadedFile) {
            if ($deleteTmpFile) {
                return move_uploaded_file($this->tmp_name, $fileName);
            } else {
                if (is_uploaded_file($this->tmp_name)) {
                    return copy($this->tmp_name, $fileName);
                } else {
                    return false;
                }
            }
        } else {
            if ($deleteTmpFile) {
                return rename($this->tmp_name, $fileName);
            } else {
                return copy($this->tmp_name, $fileName);
            }
        }
    }
    
}