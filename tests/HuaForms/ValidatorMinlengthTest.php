<?php

namespace Tests\HuaForms;

require_once dirname(__FILE__).'/HuaFormsTestCase.php';

class ValidatorMinlengthTest extends \Tests\HuaForms\HuaFormsTestCase
{
    
    /**
     * Champ avec taille minimale : OK
     */
    public function testMinlengthOk() : void
    {
        $html = <<<HTML
<form method="post" action="">
    <input type="text" name="field1" minlength="5"/>
    <button type="submit" name="ok">OK</button>
</form>
HTML;
        $_POST = ['csrf' => 'test', 'ok' => true, 'field1' => '12345'];
        
        $form = $this->buildTestForm($html);
        
        $this->assertTrue($form->validate());
        $this->assertEmpty($form->handler()->getErrorMessages());
        $this->assertEquals(['field1' => '12345'], $form->exportValues());
        $this->assertEquals(['field' => 'field1', 'type' => 'minlength', 'minlength' => 5],
            $form->getDescription()['rules'][0]);
    }
    
    /**
     * Champ avec taille minimale : Erreur
     */
    public function testMinlengthError() : void
    {
        $html = <<<HTML
<form method="post" action="">
    <input type="text" name="field1" minlength="5" />
    <button type="submit" name="ok">OK</button>
</form>
HTML;
        $_POST = ['csrf' => 'test', 'ok' => true, 'field1' => '1234'];
        
        $form = $this->buildTestForm($html);
        
        $this->assertFalse($form->validate());
        $this->assertEquals([
            'field1' => ['field1: minimum 5 characters']
        ], $form->handler()->getErrorMessages());
        $this->assertEmpty($form->exportValues());
        $this->assertEquals(['field' => 'field1', 'type' => 'minlength', 'minlength' => 5],
            $form->getDescription()['rules'][0]);
    }
    
    /**
     * Champ avec taille minimale : Erreur
     */
    public function testMinlengthErrorCustom() : void
    {
        $html = <<<HTML
<form method="post" action="">
    <input type="text" name="field1" minlength="5" minlength-message="Too short"/>
    <button type="submit" name="ok">OK</button>
</form>
HTML;
        $_POST = ['csrf' => 'test', 'ok' => true, 'field1' => '1234'];
        
        $form = $this->buildTestForm($html);
        
        $this->assertFalse($form->validate());
        $this->assertEquals([
            'field1' => ['Too short']
        ], $form->handler()->getErrorMessages());
        $this->assertEmpty($form->exportValues());
        $this->assertEquals(['field' => 'field1', 'type' => 'minlength', 'minlength' => 5, 'message' => 'Too short'],
            $form->getDescription()['rules'][0]);
    }
    
    /**
     * Exception si minlength sur un tableau
     */
    public function testMinLengthOnArray() : void
    {
        $html = <<<HTML
<form method="post" action="">
    <select name="field1" multiple minlength="3">
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