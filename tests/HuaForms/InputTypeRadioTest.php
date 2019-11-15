<?php

namespace Tests\HuaForms;

require_once dirname(__FILE__).'/HuaFormsTestCase.php';

class InputTypeRadioTest extends \Tests\HuaForms\HuaFormsTestCase
{
    
    /**
     * Test boutons radio
     */
    public function testRadioOk() : void
    {
        $html = <<<HTML
<form method="post" action="">
    <div>
        <label>Val 1</label>
        <input type="radio" name="field" value="val1"/>
    </div>
    <div>
        <label>Val 2</label>
        <input type="radio" name="field" value="val2"/>
    </div>
    <div>
        <label>Val 3</label>
        <input type="radio" name="field" value="val3"/>
    </div>
    <button type="submit" name="ok">OK</button>
</form>
HTML;
        $_POST = ['csrf' => 'test', 'ok' => true, 'field' => 'val2'];
        
        $form = $this->buildTestForm($html);
        
        $this->assertTrue($form->isSubmitted());
        $this->assertTrue($form->validate());
        $this->assertEmpty($form->handler()->getErrorMessages());
        $this->assertEquals(['field' => 'val2'], $form->exportValues());
        
        // Test de rendu du formulaire
        
        $expected = <<<HTML
<form method="post" action="">
<input type="hidden" name="csrf" value="test"/>
    <div>
        <label for="field">Val 1</label>
        <input type="radio" name="field" value="val1" id="field"/>
    </div>
    <div>
        <label for="field2">Val 2</label>
        <input type="radio" name="field" value="val2" id="field2" checked/>
    </div>
    <div>
        <label for="field3">Val 3</label>
        <input type="radio" name="field" value="val3" id="field3"/>
    </div>
    <button type="submit" name="ok" id="ok">OK</button>
</form>
HTML;
        $this->assertSame($expected, $form->render());
        
    }
    
    /**
     * Test boutons radio : valeur invalide
     */
    public function testRadioInvalidValue() : void
    {
        $html = <<<HTML
<form method="post" action="">
    <div>
        <label>Val 1</label>
        <input type="radio" name="field" value="val1"/>
    </div>
    <div>
        <label>Val 2</label>
        <input type="radio" name="field" value="val2"/>
    </div>
    <div>
        <label>Val 3</label>
        <input type="radio" name="field" value="val3"/>
    </div>
    <button type="submit" name="ok">OK</button>
</form>
HTML;
        $_POST = ['csrf' => 'test', 'ok' => true, 'field' => 'val4'];
        
        $form = $this->buildTestForm($html);
        
        $this->assertFalse($form->validate());
        $this->assertEquals([
            'field' => ['Val 1: value is not in the authorized values list (val1, val2, val3)']
        ], $form->handler()->getErrorMessages());
        $this->assertEmpty($form->exportValues());
        $this->assertEquals(['field' => 'field', 'type' => 'inarray', 'values' => ['val1', 'val2', 'val3']],
            $form->getDescription()['rules'][0]);
        
        // Test de rendu du formulaire
        
        $expected = <<<HTML
<form method="post" action="">
<input type="hidden" name="csrf" value="test"/>
<div>Val 1: value is not in the authorized values list (val1, val2, val3)</div>    <div>
        <label for="field">Val 1</label>
        <input type="radio" name="field" value="val1" id="field"/>
    </div>
    <div>
        <label for="field2">Val 2</label>
        <input type="radio" name="field" value="val2" id="field2"/>
    </div>
    <div>
        <label for="field3">Val 3</label>
        <input type="radio" name="field" value="val3" id="field3"/>
    </div>
    <button type="submit" name="ok" id="ok">OK</button>
</form>
HTML;
        $this->assertSame($expected, $form->render());
        
    }
    
}