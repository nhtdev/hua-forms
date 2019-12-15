<?php

namespace Tests\HuaForms;

require_once dirname(__FILE__).'/HuaFormsTestCase.php';

class InputTypeHiddenTest extends \Tests\HuaForms\HuaFormsTestCase
{
    
    /**
     * Test champ hidden frozen
     */
    public function testFrozenHidden() : void
    {
        $html = <<<HTML
<form method="post" action="">
    <input type="hidden" name="hidden" id="hidden" value="initiale"/>
    <button type="submit" name="ok">OK</button>
</form>
HTML;
        $_POST = ['csrf' => 'test', 'ok' => true, 'hidden' => "Test"];
        
        $form = $this->buildTestForm($html);
        
        $this->assertTrue($form->isSubmitted());
        $this->assertTrue($form->validate());
        $this->assertEmpty($form->handler()->getErrorMessages());
        $this->assertEquals(['hidden' => "initiale"], $form->exportValues());
        
        // Test de rendu du formulaire
        
        $expected = <<<HTML
<form method="post" action="">
<input type="hidden" name="csrf" value="test"/>
    <input type="hidden" name="hidden" id="hidden" value="initiale"/>
    <button type="submit" name="ok" id="ok">OK</button>
</form>
HTML;
        $this->assertEquals($expected, $form->render());
        
    }
    
    /**
     * Test champ hidden non frozen
     */
    public function testNonFrozenHidden() : void
    {
        $html = <<<HTML
<form method="post" action="">
    <input type="hidden" name="hidden" id="hidden" frozen="false"/>
    <button type="submit" name="ok">OK</button>
</form>
HTML;
        $_POST = ['csrf' => 'test', 'ok' => true, 'hidden' => "Test"];
        
        $form = $this->buildTestForm($html);
        
        $this->assertTrue($form->isSubmitted());
        $this->assertTrue($form->validate());
        $this->assertEmpty($form->handler()->getErrorMessages());
        $this->assertEquals(['hidden' => "Test"], $form->exportValues());
        
        // Test de rendu du formulaire
        
        $expected = <<<HTML
<form method="post" action="">
<input type="hidden" name="csrf" value="test"/>
    <input type="hidden" name="hidden" id="hidden" value="Test"/>
    <button type="submit" name="ok" id="ok">OK</button>
</form>
HTML;
        $this->assertEquals($expected, $form->render());
        
    }
    
    
}