<?php

namespace Tests\HuaForms;

require_once dirname(__FILE__).'/HuaFormsTestCase.php';

class InputTypeTelTest extends \Tests\HuaForms\HuaFormsTestCase
{
    
    /**
     * Test de validation d'un formulaire simple
     */
    public function testFormSubmit() : void
    {
        $html = <<<HTML
<form method="post" action="">
    <input type="tel" name="tel" id="tel"/>
    <button type="submit" name="ok">OK</button>
</form>
HTML;
        $_POST = ['csrf' => 'test', 'ok' => true, 'tel' => "01 23 45 67 89"];
        
        $form = $this->buildTestForm($html);
        
        $this->assertTrue($form->isSubmitted());
        $this->assertTrue($form->validate());
        $this->assertEmpty($form->handler()->getErrorMessages());
        $this->assertEquals(['tel' => "01 23 45 67 89"], $form->exportValues());
        
        // Test de rendu du formulaire
        
        $expected = <<<HTML
<form method="post" action="">
<input type="hidden" name="csrf" value="test"/>
    <input type="tel" name="tel" id="tel" value="01 23 45 67 89"/>
    <button type="submit" name="ok" id="ok">OK</button>
</form>
HTML;
        $this->assertSame($expected, $form->render());
        
    }
    
    
}