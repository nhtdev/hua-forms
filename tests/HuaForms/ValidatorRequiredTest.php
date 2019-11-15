<?php

namespace Tests\HuaForms;

require_once dirname(__FILE__).'/HuaFormsTestCase.php';

class ValidatorRequiredTest extends \Tests\HuaForms\HuaFormsTestCase
{
    
    /**
     * Test de validation d'un formulaire simple
     */
    public function testFormSubmit() : void
    {
        $html = <<<HTML
<form method="post" action="">
    <input type="text" name="field1" required/>
    <select name="field2"> 
        <option value="a">Option A</option>
        <option value="b">Option B</option>
    </select>
    <button type="submit" name="ok">OK</button>
</form>
HTML;
        $_POST = ['csrf' => 'test', 'ok' => true, 'field1' => 'Value1', 'field2' => 'b', 'field3' => 'nonexistingfield'];
        
        $form = $this->buildTestForm($html);
        
        $this->assertTrue($form->isSubmitted());
        $this->assertTrue($form->validate());
        $this->assertEmpty($form->handler()->getErrorMessages());
        $this->assertEquals(['field1' => 'Value1', 'field2' => 'b'], $form->exportValues());
        $this->assertEquals(['field' => 'field1', 'type' => 'required'],
            $form->getDescription()['rules'][0]);
        
        // Test de rendu du formulaire
        
        $expected = <<<HTML
<form method="post" action="">
<input type="hidden" name="csrf" value="test"/>
    <input type="text" name="field1" required id="field1" value="Value1"/>
    <select name="field2" id="field2"> 
        <option value="a">Option A</option>
        <option value="b" selected>Option B</option>
    </select>
    <button type="submit" name="ok" id="ok">OK</button>
</form>
HTML;
        $this->assertSame($expected, $form->render());
        
        
    }
    
    /**
     * Champs obligatoires : erreur
     */
    public function testRequiredField() : void
    {
        $html = <<<HTML
<form method="post" action="">
    <div form-errors class="errors"></div>
    <label for="field1">Field 1</label>
    <input type="text" name="field1" id="field1" required/>
    <input type="text" name="field2" required required-message="Field 2 mandatory"/>
    <button type="submit" name="ok">OK</button>
</form>
HTML;
        $_POST = ['csrf' => 'test', 'ok' => true, 'field1' => '', 'field2' => ''];
        
        $form = $this->buildTestForm($html);
        
        $this->assertTrue($form->isSubmitted());
        $this->assertFalse($form->validate());
        $this->assertEquals([
            'field1' => ['Field 1: field is required'],
            'field2' => ['Field 2 mandatory']
        ], $form->handler()->getErrorMessages());
        $this->assertEmpty($form->exportValues());
        
        // Test de rendu du formulaire
        
        $expected = <<<HTML
<form method="post" action="">
<input type="hidden" name="csrf" value="test"/>
   <div class="errors">Field 1: field is required<br />
Field 2 mandatory</div>    <label for="field1">Field 1</label>
    <input type="text" name="field1" id="field1" required value=""/>
    <input type="text" name="field2" required id="field2" value=""/>
    <button type="submit" name="ok" id="ok">OK</button>
</form>
HTML;
        $this->assertSame($expected, $form->render());
    }
    
}