<?php

namespace Tests\HuaForms;

require_once dirname(__FILE__).'/HuaFormsTestCase.php';

class InputTypeCheckboxTest extends \Tests\HuaForms\HuaFormsTestCase
{
    
    /**
     * Test cocher une case
     */
    public function testCheck() : void
    {
        $html = <<<HTML
<form method="post" action="">
    <input type="checkbox" name="checkbox" id="checkbox"/>
    <button type="submit" name="ok">OK</button>
</form>
HTML;
        $_POST = ['csrf' => 'test', 'ok' => true, 'checkbox' => "on"];
        
        $form = $this->buildTestForm($html);
        
        $this->assertTrue($form->isSubmitted());
        $this->assertTrue($form->validate());
        $this->assertEmpty($form->handler()->getErrorMessages());
        $this->assertEquals(['checkbox' => true], $form->exportValues());
        $this->assertIsBool($form->exportValues()['checkbox']);
        
        // Test de rendu du formulaire
        
        $expected = <<<HTML
<form method="post" action="">
<input type="hidden" name="csrf" value="test"/>
    <input type="checkbox" name="checkbox" id="checkbox" checked/>
    <button type="submit" name="ok" id="ok">OK</button>
</form>
HTML;
        $this->assertSame($expected, $form->render());
        
    }
    
    /**
     * Test décocher une case
     */
    public function testUncheck() : void
    {
        $html = <<<HTML
<form method="post" action="">
    <input type="checkbox" name="checkbox" id="checkbox" checked/>
    <button type="submit" name="ok">OK</button>
</form>
HTML;
        
        $_POST = ['csrf' => 'test', 'ok' => true];
        
        $form = $this->buildTestForm($html);
        
        $this->assertTrue($form->isSubmitted());
        $this->assertTrue($form->validate());
        $this->assertEmpty($form->handler()->getErrorMessages());
        $this->assertEquals(['checkbox' => false], $form->exportValues());
        $this->assertIsBool($form->exportValues()['checkbox']);
        
        // Test de rendu du formulaire
        
        $expected = <<<HTML
<form method="post" action="">
<input type="hidden" name="csrf" value="test"/>
    <input type="checkbox" name="checkbox" id="checkbox"/>
    <button type="submit" name="ok" id="ok">OK</button>
</form>
HTML;
        $this->assertSame($expected, $form->render());
        
    }
    
    /**
     * Test cocher une case avec valeur custom
     */
    public function testCheckValue() : void
    {
        $html = <<<HTML
<form method="post" action="">
    <input type="checkbox" name="checkbox" id="checkbox" value="coche"/>
    <button type="submit" name="ok">OK</button>
</form>
HTML;
        $_POST = ['csrf' => 'test', 'ok' => true, 'checkbox' => "coche"];
        
        $form = $this->buildTestForm($html);
        
        $this->assertTrue($form->isSubmitted());
        $this->assertTrue($form->validate());
        $this->assertEmpty($form->handler()->getErrorMessages());
        $this->assertEquals(['checkbox' => 'coche'], $form->exportValues());
        $this->assertIsString($form->exportValues()['checkbox']);
        
        // Test de rendu du formulaire
        
        $expected = <<<HTML
<form method="post" action="">
<input type="hidden" name="csrf" value="test"/>
    <input type="checkbox" name="checkbox" id="checkbox" value="coche" checked/>
    <button type="submit" name="ok" id="ok">OK</button>
</form>
HTML;
        $this->assertSame($expected, $form->render());
        
    }
    
    /**
     * Test cocher plusieurs cases
     */
    public function testCheckMultiple() : void
    {
        $html = <<<HTML
<form method="post" action="">
    <input type="checkbox" name="checkbox[]" value="val1"/>
    <input type="checkbox" name="checkbox[]" value="val2"/>
    <input type="checkbox" name="checkbox[]" value="val3"/>
    <button type="submit" name="ok">OK</button>
</form>
HTML;
        $_POST = ['csrf' => 'test', 'ok' => true, 'checkbox' => ['val1', 'val3']];
        
        $form = $this->buildTestForm($html);
        
        $this->assertTrue($form->isSubmitted());
        $this->assertTrue($form->validate());
        $this->assertEmpty($form->handler()->getErrorMessages());
        $this->assertEquals(['checkbox' => ['val1', 'val3']], $form->exportValues());
        
        // Test de rendu du formulaire
        
        $expected = <<<HTML
<form method="post" action="">
<input type="hidden" name="csrf" value="test"/>
    <input type="checkbox" name="checkbox[]" value="val1" checked/>
    <input type="checkbox" name="checkbox[]" value="val2"/>
    <input type="checkbox" name="checkbox[]" value="val3" checked/>
    <button type="submit" name="ok" id="ok">OK</button>
</form>
HTML;
        $this->assertSame($expected, $form->render());
        
    }
    
}