<?php

namespace Tests\HuaForms;

require_once dirname(__FILE__).'/HuaFormsTestCase.php';

class CSRFTest extends \Tests\HuaForms\HuaFormsTestCase
{
    
    /**
     * Test de validation d'un formulaire simple
     */
    public function testCsrfOk() : void
    {
        $html = <<<HTML
<form method="post" action="">
    <input type="text" name="field1"/>
    <button type="submit" name="ok">OK</button>
</form>
HTML;
        $_POST = ['csrf' => 'test', 'ok' => true, 'field1' => 'Value1'];
        
        $form = $this->buildTestForm($html);
        
        $this->assertEquals($form->isSubmitted(), true);
        $this->assertEquals($form->validate(), true);
        $this->assertEmpty($form->handler()->getErrorMessages());
        $this->assertEquals(['field1' => 'Value1'], $form->exportValues());
        
    }
    
    /**
     * Erreur CSRF
     */
    public function testCsrfError() : void
    {
        $html = <<<HTML
<form method="post" action="">
    <input type="text" name="field1"/>
    <button type="submit" name="ok">OK</button>
</form>
HTML;
        $_POST = ['csrf' => 'testXXXXXXXXX', 'ok' => true, 'field1' => 'Value1'];
        
        $form = $this->buildTestForm($html);
        
        $this->assertEquals($form->isSubmitted(), true);
        $this->assertEquals($form->validate(), false);
        $this->assertEquals([
            '' => ['Invalid CSRF token'],
        ], $form->handler()->getErrorMessages());
        $this->assertEmpty($form->exportValues());
        
    }
    
}