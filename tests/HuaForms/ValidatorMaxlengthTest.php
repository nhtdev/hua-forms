<?php

namespace Tests\HuaForms;

require_once dirname(__FILE__).'/HuaFormsTestCase.php';

class ValidatorMaxlengthTest extends \Tests\HuaForms\HuaFormsTestCase
{
    
    /**
     * Champ avec taille maximale : OK
     */
    public function testMaxlengthOk() : void
    {
        $html = <<<HTML
<form method="post" action="">
    <input type="text" name="field1" maxlength="15"/>
    <button type="submit" name="ok">OK</button>
</form>
HTML;
        $_POST = ['csrf' => 'test', 'ok' => true, 'field1' => '123456789012ééé'];
        
        $form = $this->buildTestForm($html);
        
        $this->assertTrue($form->validate());
        $this->assertEmpty($form->handler()->getErrorMessages());
        $this->assertEquals(['field1' => '123456789012ééé'], $form->exportValues());
        $this->assertEquals(['field' => 'field1', 'type' => 'maxlength', 'maxlength' => 15],
            $form->getDescription()['rules'][0]);
    }
    
    /**
     * Champ avec taille maximale : Erreur
     */
    public function testMaxlengthError() : void
    {
        $html = <<<HTML
<form method="post" action="">
    <input type="text" name="field1" maxlength="15"/>
    <button type="submit" name="ok">OK</button>
</form>
HTML;
        $_POST = ['csrf' => 'test', 'ok' => true, 'field1' => '1234567890123456'];
        
        $form = $this->buildTestForm($html);
        
        $this->assertFalse($form->validate());
        $this->assertEquals([
            'field1' => ['field1: maximum 15 characters']
        ], $form->handler()->getErrorMessages());
        $this->assertEmpty($form->exportValues());
        $this->assertEquals(['field' => 'field1', 'type' => 'maxlength', 'maxlength' => 15],
            $form->getDescription()['rules'][0]);
    }
    
    /**
     * Champ avec taille maximale : Erreur
     */
    public function testMaxlengthErrorCustom() : void
    {
        $html = <<<HTML
<form method="post" action="">
    <input type="text" name="field1" maxlength="15" maxlength-message="Too long"/>
    <button type="submit" name="ok">OK</button>
</form>
HTML;
        $_POST = ['csrf' => 'test', 'ok' => true, 'field1' => '1234567890123456'];
        
        $form = $this->buildTestForm($html);
        
        $this->assertFalse($form->validate());
        $this->assertEquals([
            'field1' => ['Too long']
        ], $form->handler()->getErrorMessages());
        $this->assertEmpty($form->exportValues());
        $this->assertEquals(['field' => 'field1', 'type' => 'maxlength', 'maxlength' => 15, 'message' => 'Too long'],
            $form->getDescription()['rules'][0]);
    }
    
    /**
     * Exception si maxlength sur un tableau
     */
    public function testMaxLengthOnArray() : void
    {
        $html = <<<HTML
<form method="post" action="">
    <select name="field1[]" multiple maxlength="3">
        <option value="a">Option A</option>
        <option value="b">Option B</option>
    </select>
    <button type="submit" name="ok">OK</button>
</form>
HTML;
        $_POST = ['csrf' => 'test', 'ok' => true, 'field1' => ['a', 'b']];
        
        $form = $this->buildTestForm($html);
        
        $this->expectException(\InvalidArgumentException::class);
        $form->validate();
    }
    
}