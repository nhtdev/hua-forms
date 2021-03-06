<?php

namespace Tests\HuaForms;

require_once dirname(__FILE__).'/HuaFormsTestCase.php';

class ValidatorEmailTest extends \Tests\HuaForms\HuaFormsTestCase
{
    
    /**
     * Champ texte avec règle email OK
     */
    public function testTextEmailOk() : void
    {
        $html = <<<HTML
<form method="post" action="">
    <input type="text" email name="field1" />
    <button type="submit" name="ok">OK</button>
</form>
HTML;
        $_POST = ['csrf' => 'test', 'ok' => true, 'field1' => 'test@gmail.com'];
        
        $form = $this->buildTestForm($html);
        
        $this->assertTrue($form->validate());
        $this->assertEmpty($form->handler()->getErrorMessages());
        $this->assertEquals(['field1' => 'test@gmail.com'], $form->exportValues());
        $this->assertEquals(['field' => 'field1', 'type' => 'email'], 
            $form->getDescription()['rules'][0]);
        
    }
    
    /**
     * Champ texte avec règle email Erreur
     */
    public function testTextEmailError() : void
    {
        $html = <<<HTML
<form method="post" action="">
    <input type="text" email name="field1" />
    <button type="submit" name="ok">OK</button>
</form>
HTML;
        $_POST = ['csrf' => 'test', 'ok' => true, 'field1' => 'testemailhs'];
        
        $form = $this->buildTestForm($html);
        
        $this->assertFalse($form->validate());
        $this->assertEquals([
            'field1' => ['field1: invalid email']
        ], $form->handler()->getErrorMessages());
        $this->assertEmpty($form->exportValues());
        $this->assertEquals(['field' => 'field1', 'type' => 'email'], 
            $form->getDescription()['rules'][0]);
        
        // Test de rendu du formulaire
        
        $expected = <<<HTML
<form method="post" action="">
<input type="hidden" name="csrf" value="test"/>
<div>field1: invalid email</div>    <input type="text" name="field1" id="field1" value="testemailhs"/>
    <button type="submit" name="ok" id="ok">OK</button>
</form>
HTML;
        $this->assertEquals($expected, $form->render());
    }
    
    /**
     * Champ email OK
     */
    public function testEmailOk() : void
    {
        $html = <<<HTML
<form method="post" action="">
    <input type="email" name="field1" />
    <button type="submit" name="ok">OK</button>
</form>
HTML;
        $_POST = ['csrf' => 'test', 'ok' => true, 'field1' => 'test@gmail.com'];
        
        $form = $this->buildTestForm($html);
        
        $this->assertTrue($form->validate());
        $this->assertEmpty($form->handler()->getErrorMessages());
        $this->assertEquals(['field1' => 'test@gmail.com'], $form->exportValues());
        $this->assertEquals(['field' => 'field1', 'type' => 'email'],
            $form->getDescription()['rules'][0]);
        
    }
    
    /**
     * Champ texte avec règle email Erreur + custom error
     */
    public function testEmailErrorCustom() : void
    {
        $html = <<<HTML
<form method="post" action="">
    <input type="email" name="field1" email-message="Email incorrect" />
    <button type="submit" name="ok">OK</button>
</form>
HTML;
        $_POST = ['csrf' => 'test', 'ok' => true, 'field1' => 'testemailhs'];
        
        $form = $this->buildTestForm($html);
        
        $this->assertFalse($form->validate());
        $this->assertEquals([
            'field1' => ['Email incorrect']
        ], $form->handler()->getErrorMessages());
        $this->assertEmpty($form->exportValues());
        $this->assertEquals(['field' => 'field1', 'type' => 'email', 'message' => 'Email incorrect'],
            $form->getDescription()['rules'][0]);
    }
    
}