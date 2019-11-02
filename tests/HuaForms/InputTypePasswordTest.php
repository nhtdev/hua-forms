<?php

namespace Tests\HuaForms;

require_once dirname(__FILE__).'/HuaFormsTestCase.php';

class InputTypePasswordTest extends \Tests\HuaForms\HuaFormsTestCase
{
    
    /**
     * Test de validation d'un formulaire simple
     */
    public function testFormSubmit() : void
    {
        $html = <<<HTML
<form method="post" action="">
    <input type="password" name="password" id="password"/>
    <button type="submit" name="ok">OK</button>
</form>
HTML;
        $_POST = ['csrf' => 'test', 'ok' => true, 'password' => "Test"];
        
        $form = $this->buildTestForm($html);
        
        $this->assertTrue($form->isSubmitted());
        $this->assertTrue($form->validate());
        $this->assertEmpty($form->handler()->getErrorMessages());
        $this->assertEquals(['password' => "Test"], $form->exportValues());
        
        // Test de rendu du formulaire
        
        $expected = <<<HTML
<form method="post" action="">
<input type="hidden" name="csrf" value="test"/>
    <input type="password" name="password" id="password" value="Test"/>
    <button type="submit" name="ok" id="ok">OK</button>
</form>
HTML;
        $this->assertSame($expected, $form->render());
        
    }
    
    
}