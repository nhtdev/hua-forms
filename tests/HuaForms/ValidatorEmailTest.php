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
        
        $this->assertEquals($form->isSubmitted(), true);
        $this->assertEquals($form->validate(), true);
        $this->assertEmpty($form->handler()->getErrorMessages());
        $this->assertEquals(['field1' => 'test@gmail.com'], $form->exportValues());
        $this->assertEquals([['type' => 'email']], 
            $form->getDescription()['fields'][0]['rules']);
        
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
        
        $this->assertEquals($form->isSubmitted(), true);
        $this->assertEquals($form->validate(), false);
        $this->assertEquals([
            'field1' => [': invalid email']
        ], $form->handler()->getErrorMessages());
        $this->assertEmpty($form->exportValues());
        $this->assertEquals([['type' => 'email']], 
            $form->getDescription()['fields'][0]['rules']);
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
        
        $this->assertEquals($form->isSubmitted(), true);
        $this->assertEquals($form->validate(), true);
        $this->assertEmpty($form->handler()->getErrorMessages());
        $this->assertEquals(['field1' => 'test@gmail.com'], $form->exportValues());
        $this->assertEquals([['type' => 'email']],
            $form->getDescription()['fields'][0]['rules']);
        
    }
    
    /**
     * Champ texte avec règle email Erreur
     */
    public function testEmailError() : void
    {
        $html = <<<HTML
<form method="post" action="">
    <input type="email" name="field1" />
    <button type="submit" name="ok">OK</button>
</form>
HTML;
        $_POST = ['csrf' => 'test', 'ok' => true, 'field1' => 'testemailhs'];
        
        $form = $this->buildTestForm($html);
        
        $this->assertEquals($form->isSubmitted(), true);
        $this->assertEquals($form->validate(), false);
        $this->assertEquals([
            'field1' => [': invalid email']
        ], $form->handler()->getErrorMessages());
        $this->assertEmpty($form->exportValues());
        $this->assertEquals([['type' => 'email']],
            $form->getDescription()['fields'][0]['rules']);
    }
    
}