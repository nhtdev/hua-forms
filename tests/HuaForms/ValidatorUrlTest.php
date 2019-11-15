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
        $this->assertEquals(['field' => 'field1', 'type' => 'url'], 
            $form->getDescription()['rules'][0]);
        
        // Test de rendu du formulaire
        
        $expected = <<<HTML
<form method="post" action="">
<input type="hidden" name="csrf" value="test"/>
    <input type="text" name="field1" id="field1" value="https://www.domain.fr/test.php"/>
    <button type="submit" name="ok" id="ok">OK</button>
</form>
HTML;
        $this->assertEquals($expected, $form->render());
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
            'field1' => ['field1: invalid url']
        ], $form->handler()->getErrorMessages());
        $this->assertEmpty($form->exportValues());
        $this->assertEquals(['field' => 'field1', 'type' => 'url'], 
            $form->getDescription()['rules'][0]);
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
        $this->assertEquals(['field' => 'field1', 'type' => 'url'],
            $form->getDescription()['rules'][0]);
        
    }
    
    /**
     * Champ texte avec règle url Erreur + custom error
     */
    public function testUrlErrorCustom() : void
    {
        $html = <<<HTML
<form method="post" action="">
    <input type="url" name="field1" url-message="Adresse url incorrecte" />
    <button type="submit" name="ok">OK</button>
</form>
HTML;
        $_POST = ['csrf' => 'test', 'ok' => true, 'field1' => 'testurlhs'];
        
        $form = $this->buildTestForm($html);
        
        $this->assertFalse($form->validate());
        $this->assertEquals([
            'field1' => ['Adresse url incorrecte']
        ], $form->handler()->getErrorMessages());
        $this->assertEmpty($form->exportValues());
        $this->assertEquals(['field' => 'field1', 'type' => 'url', 'message' => 'Adresse url incorrecte'],
            $form->getDescription()['rules'][0]);
    }
    
}