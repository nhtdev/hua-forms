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
        $this->assertEquals($expected, $form->render());
        
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
        $this->assertEquals($expected, $form->render());
        
    }
    
    /**
     * Test envoyer une valeur invalide sur une case à cocher
     */
    public function testCheckInvalidValue() : void
    {
        $html = <<<HTML
<form method="post" action="">
    <input type="checkbox" name="checkbox" id="checkbox"/>
    <button type="submit" name="ok">OK</button>
</form>
HTML;
        $_POST = ['csrf' => 'test', 'ok' => true, 'checkbox' => "test"];
        
        $form = $this->buildTestForm($html);
        
        $this->assertFalse($form->validate());
        $this->assertEquals([
            'checkbox' => ['checkbox: value is not allowed']
        ], $form->handler()->getErrorMessages());
        $this->assertEmpty($form->exportValues());
        $this->assertEquals(['field' => 'checkbox', 'type' => 'inarray', 'values' => ['on', '']],
            $form->getDescription()['rules'][0]);
        
    }
    
    /**
     * Test envoyer une valeur invalide sur une case à cocher
     */
    public function testCheckInvalidValueCustom() : void
    {
        $html = <<<HTML
<form method="post" action="">
    <input type="checkbox" name="checkbox" id="checkbox" value="coche"/>
    <button type="submit" name="ok">OK</button>
</form>
HTML;
        $_POST = ['csrf' => 'test', 'ok' => true, 'checkbox' => "test"];
        
        $form = $this->buildTestForm($html);
        
        $this->assertFalse($form->validate());
        $this->assertEquals([
            'checkbox' => ['checkbox: value is not allowed']
        ], $form->handler()->getErrorMessages());
        $this->assertEmpty($form->exportValues());
        $this->assertEquals(['field' => 'checkbox', 'type' => 'inarray', 'values' => ['coche', '']],
            $form->getDescription()['rules'][0]);
        
    }
    
    /**
     * Test cocher plusieurs cases
     */
    public function testCheckMultiple() : void
    {
        $html = <<<HTML
<form method="post" action="">
    <div>
        <label>Val 1</label>
        <input type="checkbox" name="checkbox[]" value="val1"/>
    </div>
    <div>
        <label>Val 2</label>
        <input type="checkbox" name="checkbox[]" value="val2"/>
    </div>
    <div>
        <label>Val 3</label>
        <input type="checkbox" name="checkbox[]" value="val3"/>
    </div>
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
    <div>
        <label for="checkbox">Val 1</label>
        <input type="checkbox" name="checkbox[]" value="val1" id="checkbox" checked/>
    </div>
    <div>
        <label for="checkbox2">Val 2</label>
        <input type="checkbox" name="checkbox[]" value="val2" id="checkbox2"/>
    </div>
    <div>
        <label for="checkbox3">Val 3</label>
        <input type="checkbox" name="checkbox[]" value="val3" id="checkbox3" checked/>
    </div>
    <button type="submit" name="ok" id="ok">OK</button>
</form>
HTML;
        $this->assertEquals($expected, $form->render());
        
    }
    
    /**
     * Test cocher plusieurs cases => Valeur invalide
     */
    public function testCheckMultipleInvalidValue() : void
    {
        $html = <<<HTML
<form method="post" action="">
    <div>
        <label>Val 1</label>
        <input type="checkbox" name="checkbox[]" value="val1"/>
    </div>
    <div>
        <label>Val 2</label>
        <input type="checkbox" name="checkbox[]" value="val2"/>
    </div>
    <div>
        <label>Val 3</label>
        <input type="checkbox" name="checkbox[]" value="val3"/>
    </div>
    <button type="submit" name="ok">OK</button>
</form>
HTML;
        $_POST = ['csrf' => 'test', 'ok' => true, 'checkbox' => ['val1', 'val4']];
        
        $form = $this->buildTestForm($html);
        
        $this->assertFalse($form->validate());
        $this->assertEquals([
            'checkbox' => ['Val 1: value is not allowed']
        ], $form->handler()->getErrorMessages());
        $this->assertEmpty($form->exportValues());
        $this->assertEquals(['field' => 'checkbox[]', 'type' => 'inarray', 'values' => ['val1', 'val2', 'val3', '']],
            $form->getDescription()['rules'][0]);
        
        // Test de rendu du formulaire
        
        $expected = <<<HTML
<form method="post" action="">
<input type="hidden" name="csrf" value="test"/>
<div>Val 1: value is not allowed</div>    <div>
        <label for="checkbox">Val 1</label>
        <input type="checkbox" name="checkbox[]" value="val1" id="checkbox" checked/>
    </div>
    <div>
        <label for="checkbox2">Val 2</label>
        <input type="checkbox" name="checkbox[]" value="val2" id="checkbox2"/>
    </div>
    <div>
        <label for="checkbox3">Val 3</label>
        <input type="checkbox" name="checkbox[]" value="val3" id="checkbox3"/>
    </div>
    <button type="submit" name="ok" id="ok">OK</button>
</form>
HTML;
        $this->assertEquals($expected, $form->render());
        
    }
    
}