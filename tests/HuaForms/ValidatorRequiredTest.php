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
        
        $this->assertEquals($form->isSubmitted(), true);
        $this->assertEquals($form->validate(), true);
        $this->assertEmpty($form->handler()->getErrorMessages());
        $this->assertEquals(['field1' => 'Value1', 'field2' => 'b'], $form->exportValues());
        $this->assertEquals([['type' => 'required']],
            $form->getDescription()['fields'][0]['rules']);
        
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
    <input type="text" name="field1" required/>
    <input type="text" name="field2" required required-message="Field 2 mandatory"/>
    <button type="submit" name="ok">OK</button>
</form>
HTML;
        $_POST = ['csrf' => 'test', 'ok' => true, 'field1' => '', 'field2' => ''];
        
        $form = $this->buildTestForm($html);
        
        $this->assertEquals($form->isSubmitted(), true);
        $this->assertEquals($form->validate(), false);
        $this->assertEquals([
            'field1' => [': field is required'],
            'field2' => ['Field 2 mandatory']
        ], $form->handler()->getErrorMessages());
        $this->assertEmpty($form->exportValues());
        
        // Test de rendu du formulaire
        
        $expected = <<<HTML
<form method="post" action="">
<input type="hidden" name="csrf" value="test"/>
   <div class="errors">: field is required<br />
Field 2 mandatory</div>    <input type="text" name="field1" required id="field1" value=""/>
    <input type="text" name="field2" required id="field2" value=""/>
    <button type="submit" name="ok" id="ok">OK</button>
</form>
HTML;
        $this->assertSame($expected, $form->render());
    }
    
}