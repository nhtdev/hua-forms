<?php

namespace Tests\HuaForms;

require_once dirname(__FILE__).'/HuaFormsTestCase.php';

class ValidatorPatternTest extends \Tests\HuaForms\HuaFormsTestCase
{
    
    /**
     * Champ avec pattern : OK
     */
    public function testPatternOk() : void
    {
        $html = <<<HTML
<form method="post" action="">
    <input type="text" name="field1" pattern="[a-f]{4,8}"/>
    <button type="submit" name="ok">OK</button>
</form>
HTML;
        $_POST = ['csrf' => 'test', 'ok' => true, 'field1' => 'abcdef'];
        
        $form = $this->buildTestForm($html);
        
        $this->assertTrue($form->validate());
        $this->assertEmpty($form->handler()->getErrorMessages());
        $this->assertEquals(['field1' => 'abcdef'], $form->exportValues());
        $this->assertEquals(['field' => 'field1', 'type' => 'pattern', 'pattern' => '[a-f]{4,8}'],
            $form->getDescription()['rules'][0]);
    }
    
    /**
     * Champ avec taille pattern : Erreur
     */
    public function testPatternError() : void
    {
        $html = <<<HTML
<form method="post" action="">
    <input type="text" name="field1" pattern="[a-f]{4,8}"/>
    <button type="submit" name="ok">OK</button>
</form>
HTML;
        $_POST = ['csrf' => 'test', 'ok' => true, 'field1' => 'abcdefg'];
        
        $form = $this->buildTestForm($html);
        
        $this->assertFalse($form->validate());
        $this->assertEquals([
            'field1' => ['field1: invalid format']
        ], $form->handler()->getErrorMessages());
        $this->assertEmpty($form->exportValues());
        $this->assertEquals(['field' => 'field1', 'type' => 'pattern', 'pattern' => '[a-f]{4,8}'],
            $form->getDescription()['rules'][0]);
    }
    
    /**
     * Champ avec pattern : Erreur
     */
    public function testPatternErrorCustom() : void
    {
        $html = <<<HTML
<form method="post" action="">
    <input type="text" name="field1" pattern="[a-f]{4,8}" pattern-message="Incorrect"/>
    <button type="submit" name="ok">OK</button>
</form>
HTML;
        $_POST = ['csrf' => 'test', 'ok' => true, 'field1' => 'aaabbbcccddd'];
        
        $form = $this->buildTestForm($html);
        
        $this->assertFalse($form->validate());
        $this->assertEquals([
            'field1' => ['Incorrect']
        ], $form->handler()->getErrorMessages());
        $this->assertEmpty($form->exportValues());
        $this->assertEquals(['field' => 'field1', 'type' => 'pattern', 'pattern' => '[a-f]{4,8}', 'message' => 'Incorrect'],
            $form->getDescription()['rules'][0]);
    }
    
    /**
     * Exception si pattern sur un tableau
     */
    public function testPatternOnArray() : void
    {
        $html = <<<HTML
<form method="post" action="">
    <select name="field1[]" multiple pattern="[a-f]{4,8}">
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