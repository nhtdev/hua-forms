<?php

namespace Tests\HuaForms;

require_once dirname(__FILE__).'/HuaFormsTestCase.php';

class ValidatorInArrayTest extends \Tests\HuaForms\HuaFormsTestCase
{
    
    /**
     * Champ select : inarray ok
     */
    public function testInArrayOk() : void
    {
        $html = <<<HTML
<form method="post" action="">
    <select name="field1">
        <option value="a">Option A</option>
        <option value="b">Option B</option>
    </select>
    <button type="submit" name="ok">OK</button>
</form>
HTML;
        $_POST = ['csrf' => 'test', 'ok' => true, 'field1' => 'b'];
        
        $form = $this->buildTestForm($html);
        
        $this->assertEquals($form->validate(), true);
        $this->assertEmpty($form->handler()->getErrorMessages());
        $this->assertEquals(['field1' => 'b'], $form->exportValues());
        $this->assertEquals([['type' => 'inarray', 'values' => ['a', 'b']]], 
            $form->getDescription()['fields'][0]['rules']);
        
    }
    
    /**
     * Champ select : inarray error
     */
    public function testInArrayError() : void
    {
        $html = <<<HTML
<form method="post" action="">
    <select name="field1">
        <option value="a">Option A</option>
        <option value="b">Option B</option>
    </select>
    <button type="submit" name="ok">OK</button>
</form>
HTML;
        $_POST = ['csrf' => 'test', 'ok' => true, 'field1' => 'c'];
        
        $form = $this->buildTestForm($html);
        
        $this->assertEquals($form->validate(), false);
        $this->assertEquals([
            'field1' => [': value is not in the authorized values list (a, b)']
        ], $form->handler()->getErrorMessages());
        $this->assertEmpty($form->exportValues());
        $this->assertEquals([['type' => 'inarray', 'values' => ['a', 'b']]], 
            $form->getDescription()['fields'][0]['rules']);
    }
    
    /**
     * Champ select : inarray ok
     */
    public function testInArrayWithoutOptionValueOk() : void
    {
        $html = <<<HTML
<form method="post" action="">
    <select name="field1">
        <option>Option A</option>
        <option>Option B</option>
    </select>
    <button type="submit" name="ok">OK</button>
</form>
HTML;
        $_POST = ['csrf' => 'test', 'ok' => true, 'field1' => 'Option B'];
        
        $form = $this->buildTestForm($html);
        
        $this->assertEquals($form->validate(), true);
        $this->assertEmpty($form->handler()->getErrorMessages());
        $this->assertEquals(['field1' => 'Option B'], $form->exportValues());
        $this->assertEquals([['type' => 'inarray', 'values' => ['Option A', 'Option B']]], 
            $form->getDescription()['fields'][0]['rules']);
    }
    
    /**
     * Champ select : inarray error
     */
    public function testInArrayWithoutOptionValueError() : void
    {
        $html = <<<HTML
<form method="post" action="">
    <select name="field1">
        <option>Option A</option>
        <option>Option B</option>
    </select>
    <button type="submit" name="ok">OK</button>
</form>
HTML;
        $_POST = ['csrf' => 'test', 'ok' => true, 'field1' => 'Option C'];
        
        $form = $this->buildTestForm($html);
        
        $this->assertEquals($form->validate(), false);
        $this->assertEquals([
            'field1' => [': value is not in the authorized values list (Option A, Option B)']
        ], $form->handler()->getErrorMessages());
        $this->assertEmpty($form->exportValues());
        $this->assertEquals([['type' => 'inarray', 'values' => ['Option A', 'Option B']]],
            $form->getDescription()['fields'][0]['rules']);
    }
    
    /**
     * Champ select : inarray ok multiple
     */
    public function testInArrayOkMultiple() : void
    {
        $html = <<<HTML
<form method="post" action="">
    <select name="field1" multiple>
        <option value="a">Option A</option>
        <option value="b">Option B</option>
    </select>
    <button type="submit" name="ok">OK</button>
</form>
HTML;
        $_POST = ['csrf' => 'test', 'ok' => true, 'field1' => ['a', 'b']];
        
        $form = $this->buildTestForm($html);
        
        $this->assertEquals($form->validate(), true);
        $this->assertEmpty($form->handler()->getErrorMessages());
        $this->assertEquals(['field1' => ['a', 'b']], $form->exportValues());
        $this->assertEquals([['type' => 'inarray', 'values' => ['a', 'b']]],
            $form->getDescription()['fields'][0]['rules']);
    }
    
    /**
     * Champ select : inarray error multiple
     */
    public function testInArrayErrorMultiple() : void
    {
        $html = <<<HTML
<form method="post" action="">
    <select name="field1" multiple>
        <option value="a">Option A</option>
        <option value="b">Option B</option>
    </select>
    <button type="submit" name="ok">OK</button>
</form>
HTML;
        $_POST = ['csrf' => 'test', 'ok' => true, 'field1' => ['a', 'c']];
        
        $form = $this->buildTestForm($html);
        
        $this->assertEquals($form->validate(), false);
        $this->assertEquals([
            'field1' => [': value is not in the authorized values list (a, b)']
        ], $form->handler()->getErrorMessages());
        $this->assertEmpty($form->exportValues());
        $this->assertEquals([['type' => 'inarray', 'values' => ['a', 'b']]],
            $form->getDescription()['fields'][0]['rules']);
    }
    
    /**
     * Champ select : inarray sur un champ texte
     */
    public function testInArrayTextField() : void
    {
        $html = <<<HTML
<form method="post" action="">
    <input name="field1" inarray="O,N" inarray-message="Valeur incorrecte" trim/>
    <button type="submit" name="ok">OK</button>
</form>
HTML;
        $_POST = ['csrf' => 'test', 'ok' => true, 'field1' => ' O '];
        
        $form = $this->buildTestForm($html);
        
        $this->assertEquals($form->validate(), true);
        $this->assertEmpty($form->handler()->getErrorMessages());
        $this->assertEquals(['field1' => 'O'], $form->exportValues());
        $this->assertEquals([['type' => 'inarray', 'values' => ['O', 'N'], 'message' => 'Valeur incorrecte']],
            $form->getDescription()['fields'][0]['rules']);
    }
    
    /**
     * Champ select : inarray error champ texte
     */
    public function testInArrayTextFieldError() : void
    {
        $html = <<<HTML
<form method="post" action="">
    <input name="field1" inarray="O,N" inarray-message="Valeur incorrecte" trim/>
    <button type="submit" name="ok">OK</button>
</form>
HTML;
        $_POST = ['csrf' => 'test', 'ok' => true, 'field1' => 'X'];
        
        $form = $this->buildTestForm($html);
        
        $this->assertEquals($form->validate(), false);
        $this->assertEquals([
            'field1' => ['Valeur incorrecte']
        ], $form->handler()->getErrorMessages());
        $this->assertEmpty($form->exportValues());
        $this->assertEquals([['type' => 'inarray', 'values' => ['O', 'N'], 'message' => 'Valeur incorrecte']],
            $form->getDescription()['fields'][0]['rules']);
    }
    
}