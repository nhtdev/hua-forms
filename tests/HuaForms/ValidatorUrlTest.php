<?php

namespace Tests\HuaForms;

require_once dirname(__FILE__).'/HuaFormsTestCase.php';

class ValidatorUrlTest extends \Tests\HuaForms\HuaFormsTestCase
{
    
    /**
     * Champ texte avec règle url OK
     */
    public function testTextUrlOk() : void
    {
        $html = <<<HTML
<form method="post" action="">
    <input type="text" url name="field1" />
    <button type="submit" name="ok">OK</button>
</form>
HTML;
        $_POST = ['csrf' => 'test', 'ok' => true, 'field1' => 'https://www.domain.fr/test.php'];
        
        $form = $this->buildTestForm($html);
        
        $this->assertTrue($form->validate());
        $this->assertEmpty($form->handler()->getErrorMessages());
        $this->assertEquals(['field1' => 'https://www.domain.fr/test.php'], $form->exportValues());
        $this->assertEquals([['type' => 'url']], 
            $form->getDescription()['fields'][0]['rules']);
        
    }
    
    /**
     * Champ texte avec règle url Erreur
     */
    public function testTextUrlError() : void
    {
        $html = <<<HTML
<form method="post" action="">
    <input type="text" url name="field1" />
    <button type="submit" name="ok">OK</button>
</form>
HTML;
        $_POST = ['csrf' => 'test', 'ok' => true, 'field1' => 'testurlhs'];
        
        $form = $this->buildTestForm($html);
        
        $this->assertFalse($form->validate());
        $this->assertEquals([
            'field1' => [': invalid url']
        ], $form->handler()->getErrorMessages());
        $this->assertEmpty($form->exportValues());
        $this->assertEquals([['type' => 'url']], 
            $form->getDescription()['fields'][0]['rules']);
    }
    
    /**
     * Champ url OK
     */
    public function testUrlOk() : void
    {
        $html = <<<HTML
<form method="post" action="">
    <input type="url" name="field1" />
    <button type="submit" name="ok">OK</button>
</form>
HTML;
        $_POST = ['csrf' => 'test', 'ok' => true, 'field1' => 'https://www.domain.fr/test.php'];
        
        $form = $this->buildTestForm($html);
        
        $this->assertTrue($form->validate());
        $this->assertEmpty($form->handler()->getErrorMessages());
        $this->assertEquals(['field1' => 'https://www.domain.fr/test.php'], $form->exportValues());
        $this->assertEquals([['type' => 'url']],
            $form->getDescription()['fields'][0]['rules']);
        
    }
    
    /**
     * Champ texte avec règle url Erreur
     */
    public function testUrlError() : void
    {
        $html = <<<HTML
<form method="post" action="">
    <input type="url" name="field1" />
    <button type="submit" name="ok">OK</button>
</form>
HTML;
        $_POST = ['csrf' => 'test', 'ok' => true, 'field1' => 'testurlhs'];
        
        $form = $this->buildTestForm($html);
        
        $this->assertFalse($form->validate());
        $this->assertEquals([
            'field1' => [': invalid url']
        ], $form->handler()->getErrorMessages());
        $this->assertEmpty($form->exportValues());
        $this->assertEquals([['type' => 'url']],
            $form->getDescription()['fields'][0]['rules']);
    }
    
}