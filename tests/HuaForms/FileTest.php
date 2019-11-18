<?php

namespace Tests\HuaForms;

require_once dirname(__FILE__).'/HuaFormsTestCase.php';

/**
 * Test de la classe \HuaForms\File
 */
class FileTest extends \Tests\HuaForms\HuaFormsTestCase
{
    protected function makeTmpFile(int $length) : string
    {
        $tmpName = '/tmp/upload_'.uniqid();
        $f = fopen($tmpName, 'w');
        for ($i=0; $i<$length; $i++) {
            fwrite($f, 'x');
        }
        fclose($f);
        return $tmpName;
    }
    
    /**
     * Test constructeur + saveTo de la classe File, sans erreur
     */
    public function testConstructOk() : void
    {
        $tmpName = $this->makeTmpFile(30);
        $f = [
            'name' => 'Test.pdf',
            'type' => 'application/pdf',
            'tmp_name' => $tmpName,
            'size' => 30,
            'error' => UPLOAD_ERR_OK,
        ];
        $file = new \HuaForms\File($f, false);
        
        $this->assertEquals('Test.pdf', $file->name);
        $this->assertEquals('pdf', $file->getExtension());
        $this->assertEquals($tmpName, $file->tmp_name);
        $this->assertEquals('application/pdf', $file->typeClientSide);
        $this->assertEquals('text/plain', $file->typeServerSide);
        $this->assertFileExists($file->tmp_name);
        $this->assertEquals(str_repeat('x', 30), file_get_contents($file->tmp_name));
        $this->assertEquals(30, $file->size);
        $this->assertEquals(UPLOAD_ERR_OK, $file->error);
        $this->assertTrue($file->isUploaded());
        $this->assertFalse($file->hasError());
        
        // Test saveTo (copy)
        $tmpName2 = '/tmp/upload_'.uniqid();
        $result = $file->saveTo($tmpName2, false);
        $this->assertTrue($result);
        $this->assertFileExists($file->tmp_name);
        $this->assertEquals(str_repeat('x', 30), file_get_contents($file->tmp_name));
        $this->assertFileExists($tmpName2);
        $this->assertEquals(str_repeat('x', 30), file_get_contents($tmpName2));
        
        // Test saveTo (move)
        $tmpName3 = '/tmp/upload_'.uniqid();
        $result = $file->saveTo($tmpName3, true);
        $this->assertTrue($result);
        $this->assertFileNotExists($file->tmp_name);
        $this->assertFileExists($tmpName3);
        $this->assertEquals(str_repeat('x', 30), file_get_contents($tmpName3));
        
    }
    
    /**
     * Constructeur vide : erreur
     */
    public function testConstructEmpty() : void
    {
        $this->expectException(\InvalidArgumentException::class);
        $file = new \HuaForms\File([]);
    }
    
    /**
     * Création d'un fichier non uploadé
     */
    public function testConstructNotUploaded() : void
    {
        $f = [
            'name' => '',
            'type' => '',
            'tmp_name' => '',
            'size' => 0,
            'error' => UPLOAD_ERR_NO_FILE,
        ];
        $file = new \HuaForms\File($f, false);
        
        $this->assertEquals(UPLOAD_ERR_NO_FILE, $file->error);
        $this->assertFalse($file->isUploaded());
        $this->assertFalse($file->hasError()); // Non uploadé : n'est pas considéré comme une erreur
        
        // Test saveTo
        $tmpName2 = '/tmp/upload_'.uniqid();
        $result = $file->saveTo($tmpName2);
        $this->assertFalse($result);
        $this->assertFileNotExists($tmpName2);
    }
    
    /**
     * Création d'un fichier alors qu'il n'existe pas sur disque
     */
    public function testConstructNoFileOnDisk() : void
    {
        $tmpName = '/tmp/upload_'.uniqid();
        $f = [
            'name' => 'Test.pdf',
            'type' => 'application/pdf',
            'tmp_name' => $tmpName,
            'size' => 30,
            'error' => UPLOAD_ERR_OK,
        ];
        $file = new \HuaForms\File($f, false);
        
        $this->assertEquals('Test.pdf', $file->name);
        $this->assertEquals('pdf', $file->getExtension());
        $this->assertEquals($tmpName, $file->tmp_name);
        $this->assertEquals('application/pdf', $file->typeClientSide);
        $this->assertFalse($file->typeServerSide);
        $this->assertFileNotExists($file->tmp_name);
        $this->assertEquals(30, $file->size);
        $this->assertEquals(UPLOAD_ERR_NO_FILE, $file->error);
        $this->assertFalse($file->isUploaded());
        $this->assertFalse($file->hasError()); // Non uploadé : n'est pas considéré comme une erreur
        
        // Test saveTo
        $tmpName2 = '/tmp/upload_'.uniqid();
        $result = $file->saveTo($tmpName2);
        $this->assertFalse($result);
        $this->assertFileNotExists($tmpName2);
    }
    
    /**
     * Fichier en erreur
     */
    public function testFileError() : void
    {
        $tmpName = '/tmp/upload_'.uniqid();
        $f = [
            'name' => 'Test.pdf',
            'type' => 'application/pdf',
            'tmp_name' => $tmpName,
            'size' => 0,
            'error' => UPLOAD_ERR_CANT_WRITE,
        ];
        $file = new \HuaForms\File($f, false);
        
        $this->assertEquals('Test.pdf', $file->name);
        $this->assertEquals('pdf', $file->getExtension());
        $this->assertEquals($tmpName, $file->tmp_name);
        $this->assertEquals('application/pdf', $file->typeClientSide);
        $this->assertFalse($file->typeServerSide);
        $this->assertFileNotExists($file->tmp_name);
        $this->assertEquals(UPLOAD_ERR_CANT_WRITE, $file->error);
        $this->assertTrue($file->isUploaded());
        $this->assertTrue($file->hasError());
        
        // Test saveTo
        $tmpName2 = '/tmp/upload_'.uniqid();
        $result = $file->saveTo($tmpName2);
        $this->assertFalse($result);
        $this->assertFileNotExists($tmpName2);
    }
    
    /**
     * Test sauvegarde 2 fois du fichier
     */
    public function testSaveToTwice() : void
    {
        $tmpName = $this->makeTmpFile(30);
        $f = [
            'name' => 'Test.pdf',
            'type' => 'application/pdf',
            'tmp_name' => $tmpName,
            'size' => 30,
            'error' => UPLOAD_ERR_OK,
        ];
        $file = new \HuaForms\File($f, false);
        
        $this->assertFileExists($file->tmp_name);
        
        // Test saveTo 1
        $tmpName2 = '/tmp/upload_'.uniqid();
        $result = $file->saveTo($tmpName2);
        $this->assertTrue($result);
        $this->assertFileNotExists($file->tmp_name);
        $this->assertFileExists($tmpName2);
        $this->assertEquals(str_repeat('x', 30), file_get_contents($tmpName2));
        
        // Test saveTo 2
        $tmpName3 = '/tmp/upload_'.uniqid();
        $this->expectException(\RuntimeException::class);
        $result = $file->saveTo($tmpName3, true);
        
    }
    
    /**
     * Test constructeur + saveTo de la classe File, avec un fichier qui n'a pas réellement été uploadé
     */
    public function testConstructNotReallyUploaded() : void
    {
        $tmpName = $this->makeTmpFile(30);
        $f = [
            'name' => 'Test.pdf',
            'type' => 'application/pdf',
            'tmp_name' => $tmpName,
            'size' => 30,
            'error' => UPLOAD_ERR_OK,
        ];
        $file = new \HuaForms\File($f, true);
        
        $this->assertEquals('Test.pdf', $file->name);
        $this->assertEquals('pdf', $file->getExtension());
        $this->assertEquals($tmpName, $file->tmp_name);
        $this->assertEquals('application/pdf', $file->typeClientSide);
        $this->assertFalse($file->typeServerSide);
        $this->assertFileExists($file->tmp_name);
        $this->assertEquals(str_repeat('x', 30), file_get_contents($file->tmp_name));
        $this->assertEquals(30, $file->size);
        $this->assertEquals(UPLOAD_ERR_NO_FILE, $file->error);
        $this->assertFalse($file->isUploaded());
        $this->assertFalse($file->hasError());
        
        // Test saveTo
        $tmpName2 = '/tmp/upload_'.uniqid();
        $result = $file->saveTo($tmpName2);
        $this->assertFalse($result);
        $this->assertFileExists($file->tmp_name);
        $this->assertEquals(str_repeat('x', 30), file_get_contents($file->tmp_name));
        $this->assertFileNotExists($tmpName2);
   
    }
    
}