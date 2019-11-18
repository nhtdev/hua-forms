<?php

namespace Tests\HuaForms;

require_once dirname(__FILE__).'/HuaFormsTestCase.php';

class InputTypeFileTest extends \Tests\HuaForms\HuaFormsTestCase
{
    protected function system_extension_mime_types() {
        # Returns the system MIME type mapping of extensions to MIME types, as defined in /etc/mime.types.
        $out = array();
        $file = fopen('/etc/mime.types', 'r');
        while(($line = fgets($file)) !== false) {
            $line = trim(preg_replace('/#.*/', '', $line));
            if(!$line)
                continue;
                $parts = preg_split('/\s+/', $line);
                if(count($parts) == 1)
                    continue;
                    $type = array_shift($parts);
                    foreach($parts as $part)
                        $out[$part] = $type;
        }
        fclose($file);
        return $out;
    }
    
    protected function system_extension_mime_type($file) {
        # Returns the system MIME type (as defined in /etc/mime.types) for the filename specified.
        #
        # $file - the filename to examine
        static $types;
        if(!isset($types))
            $types = $this->system_extension_mime_types();
            $ext = pathinfo($file, PATHINFO_EXTENSION);
            if(!$ext)
                $ext = $file;
                $ext = strtolower($ext);
                return isset($types[$ext]) ? $types[$ext] : null;
    }
    
    protected function addFile(string $inputName, string $fileName, int $length, int $error=UPLOAD_ERR_OK) : void
    {
        $tmpName = '/tmp/upload_'.uniqid();
        if ($error === UPLOAD_ERR_OK) {
            $f = fopen($tmpName, 'w');
            for ($i=0; $i<$length; $i++) {
                fwrite($f, 'x');
            }
            fclose($f);
        }
        if (substr($inputName, -2) === '[]') {
            $inputName = str_replace('[]', '', $inputName);
            $_FILES[$inputName]['name'][] = $fileName;
            $_FILES[$inputName]['type'][] = $this->system_extension_mime_type($fileName);
            $_FILES[$inputName]['tmp_name'][] = $tmpName;
            $_FILES[$inputName]['size'][] = $length;
            $_FILES[$inputName]['error'][] = $error;
        } else {
            $_FILES[$inputName]['name'] = $fileName;
            $_FILES[$inputName]['type'] = $this->system_extension_mime_type($fileName);
            $_FILES[$inputName]['tmp_name'] = $tmpName;
            $_FILES[$inputName]['size'] = $length;
            $_FILES[$inputName]['error'] = $error;
        }
    }
    
    /**
     * Test de validation d'un formulaire simple
     */
    public function testFormSubmit() : void
    {
        $html = <<<HTML
<form method="post" action="">
    <input type="file" name="file" id="file"/>
    <button type="submit" name="ok">OK</button>
</form>
HTML;
        $_POST = ['csrf' => 'test', 'ok' => true];
        $this->addFile('file', 'Fichier.pdf', 35);
        
        $form = $this->buildTestForm($html);
        
        $this->assertTrue($form->isSubmitted());
        $this->assertTrue($form->validate());
        $this->assertEmpty($form->handler()->getErrorMessages());
        $values = $form->exportValues();
        $this->assertEquals('Fichier.pdf', $values['file']->name);
        $this->assertEquals('application/pdf', $values['file']->typeClientSide);
        $this->assertEquals('text/plain', $values['file']->typeServerSide);
        $this->assertEquals(35, $values['file']->size);
        $this->assertEquals(UPLOAD_ERR_OK, $values['file']->error);
        $this->assertEquals(str_repeat('x', 35), file_get_contents($values['file']->tmp_name));
        
        // Test de rendu du formulaire (no value)
        
        $expected = <<<HTML
<form method="post" action="" enctype="multipart/form-data">
<input type="hidden" name="csrf" value="test"/>
    <input type="file" name="file" id="file"/>
    <button type="submit" name="ok" id="ok">OK</button>
</form>
HTML;
        $this->assertEquals($expected, $form->render());
        
    }
    
    /**
     * Fichier obligatoire : erreur
     */
    public function testRequiredField() : void
    {
        $html = <<<HTML
<form method="post" action="">
    <input type="file" name="file" id="file" required/>
    <button type="submit" name="ok">OK</button>
</form>
HTML;
        $_POST = ['csrf' => 'test', 'ok' => true];
        
        $form = $this->buildTestForm($html);
        
        $this->assertTrue($form->isSubmitted());
        $this->assertFalse($form->validate());
        $this->assertEquals([
            'file' => ['file: field is required'],
        ], $form->handler()->getErrorMessages());
        $this->assertEmpty($form->exportValues());
        
        // Test de rendu du formulaire
        
        $expected = <<<HTML
<form method="post" action="" enctype="multipart/form-data">
<input type="hidden" name="csrf" value="test"/>
<div>file: field is required</div>    <input type="file" name="file" id="file" required/>
    <button type="submit" name="ok" id="ok">OK</button>
</form>
HTML;
        $this->assertEquals($expected, $form->render());
    }
    
    /**
     * Test de validation de plusieurs fichiers
     */
    public function testFormSubmitMultiple() : void
    {
        $html = <<<HTML
<form method="post" action="">
    <input type="file" name="files" id="files" required multiple/>
    <button type="submit" name="ok">OK</button>
</form>
HTML;
        $_POST = ['csrf' => 'test', 'ok' => true];
        $this->addFile('files[]', 'Fichier.pdf', 35);
        $this->addFile('files[]', 'Fichier.docx', 40);
        
        $form = $this->buildTestForm($html);
        
        $this->assertTrue($form->isSubmitted());
        $this->assertTrue($form->validate());
        $this->assertEmpty($form->handler()->getErrorMessages());
        
        $values = $form->exportValues();
        $this->assertCount(2, $values['files']);
        
        $this->assertEquals('Fichier.pdf', $values['files'][0]->name);
        $this->assertEquals('application/pdf', $values['files'][0]->typeClientSide);
        $this->assertEquals('text/plain', $values['files'][0]->typeServerSide);
        $this->assertEquals(35, $values['files'][0]->size);
        $this->assertEquals(UPLOAD_ERR_OK, $values['files'][0]->error);
        $this->assertEquals(str_repeat('x', 35), file_get_contents($values['files'][0]->tmp_name));
        
        $this->assertEquals('Fichier.docx', $values['files'][1]->name);
        $this->assertEquals('application/vnd.openxmlformats-officedocument.wordprocessingml.document', $values['files'][1]->typeClientSide);
        $this->assertEquals('text/plain', $values['files'][1]->typeServerSide);
        $this->assertEquals(40, $values['files'][1]->size);
        $this->assertEquals(UPLOAD_ERR_OK, $values['files'][1]->error);
        $this->assertEquals(str_repeat('x', 40), file_get_contents($values['files'][1]->tmp_name));
        
        // Test de rendu du formulaire (no value)
        
        $expected = <<<HTML
<form method="post" action="" enctype="multipart/form-data">
<input type="hidden" name="csrf" value="test"/>
    <input type="file" name="files[]" id="files" required multiple/>
    <button type="submit" name="ok" id="ok">OK</button>
</form>
HTML;
        $this->assertEquals($expected, $form->render());
        
    }
    
    /**
     * Fichiers obligatoires multiples : erreur
     */
    public function testRequiredFieldMultiple() : void
    {
        $html = <<<HTML
<form method="post" action="">
    <input type="file" name="files" id="files" required multiple/>
    <button type="submit" name="ok">OK</button>
</form>
HTML;
        $_POST = ['csrf' => 'test', 'ok' => true];
        
        $form = $this->buildTestForm($html);
        
        $this->assertTrue($form->isSubmitted());
        $this->assertFalse($form->validate());
        $this->assertEquals([
            'files' => ['files: field is required'],
        ], $form->handler()->getErrorMessages());
        $this->assertEmpty($form->exportValues());
        
    }
    
}