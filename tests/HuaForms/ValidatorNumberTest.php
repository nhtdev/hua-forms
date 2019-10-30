<?php

namespace Tests\HuaForms;

require_once dirname(__FILE__).'/HuaFormsTestCase.php';

class ValidatorNumberTest extends \Tests\HuaForms\HuaFormsTestCase
{
    
    /**
     * Champ number OK
     */
    public function testNumberOk() : void
    {
        $html = <<<HTML
<form method="post" action="">
    <input type="number" name="field1" />
    <button type="submit" name="ok">OK</button>
</form>
HTML;
        $_POST = ['csrf' => 'test', 'ok' => true, 'field1' => '314'];
        
        $form = $this->buildTestForm($html);
        
        $this->assertTrue($form->validate());
        $this->assertEmpty($form->handler()->getErrorMessages());
        $this->assertEquals(['field1' => '314'], $form->exportValues());
        $this->assertIsInt($form->exportValues()['field1']);
        $this->assertEquals([['type' => 'number']], 
            $form->getDescription()['fields'][0]['rules']);
        
    }
    
    /**
     * Champ number Erreur
     */
    public function testNumberError() : void
    {
        $html = <<<HTML
<form method="post" action="">
    <input type="number" name="field1" />
    <button type="submit" name="ok">OK</button>
</form>
HTML;
        $_POST = ['csrf' => 'test', 'ok' => true, 'field1' => 'test'];
        
        $form = $this->buildTestForm($html);
        
        $this->assertFalse($form->validate());
        $this->assertEquals([
            'field1' => [': value is not a valid number']
        ], $form->handler()->getErrorMessages());
        $this->assertEmpty($form->exportValues());
        $this->assertEquals([['type' => 'number']], 
            $form->getDescription()['fields'][0]['rules']);
    }
    
    /**
     * Champ number + min OK
     */
    public function testNumberMinOk() : void
    {
        $html = <<<HTML
<form method="post" action="">
    <input type="number" name="field1" min="10" max="20" />
    <button type="submit" name="ok">OK</button>
</form>
HTML;
        $_POST = ['csrf' => 'test', 'ok' => true, 'field1' => '14'];
        
        $form = $this->buildTestForm($html);
        
        $this->assertTrue($form->validate());
        $this->assertEmpty($form->handler()->getErrorMessages());
        $this->assertEquals(['field1' => '14'], $form->exportValues());
        $this->assertIsInt($form->exportValues()['field1']);
        $this->assertEquals([['type' => 'number', 'min' => 10, 'max' => 20]],
            $form->getDescription()['fields'][0]['rules']);
        
    }
    
    /**
     * Champ number + min Error
     */
    public function testNumberMinError() : void
    {
        $html = <<<HTML
<form method="post" action="">
    <input type="number" name="field1" min="10" max="20" />
    <button type="submit" name="ok">OK</button>
</form>
HTML;
        $_POST = ['csrf' => 'test', 'ok' => true, 'field1' => '4'];
        
        $form = $this->buildTestForm($html);
        
        $this->assertFalse($form->validate());
        $this->assertEquals([
            'field1' => [': value must be greater than or equal to 10']
        ], $form->handler()->getErrorMessages());
        $this->assertEmpty($form->exportValues());
        $this->assertEquals([['type' => 'number', 'min' => 10, 'max' => 20]],
            $form->getDescription()['fields'][0]['rules']);
        
    }
    
    /**
     * Champ number + max Error
     */
    public function testNumberMaxError() : void
    {
        $html = <<<HTML
<form method="post" action="">
    <input type="number" name="field1" min="10" max="20" />
    <button type="submit" name="ok">OK</button>
</form>
HTML;
        $_POST = ['csrf' => 'test', 'ok' => true, 'field1' => '24'];
        
        $form = $this->buildTestForm($html);
        
        $this->assertFalse($form->validate());
        $this->assertEquals([
            'field1' => [': value must be less than or equal to 20']
        ], $form->handler()->getErrorMessages());
        $this->assertEmpty($form->exportValues());
        $this->assertEquals([['type' => 'number', 'min' => 10, 'max' => 20]],
            $form->getDescription()['fields'][0]['rules']);
        
    }
    
    /**
     * Champ number negative Ok
     */
    public function testNumberNegativeOk() : void
    {
        $html = <<<HTML
<form method="post" action="">
    <input type="number" name="field1" />
    <button type="submit" name="ok">OK</button>
</form>
HTML;
        $_POST = ['csrf' => 'test', 'ok' => true, 'field1' => '-1'];
        
        $form = $this->buildTestForm($html);
        
        $this->assertTrue($form->validate());
        $this->assertEmpty($form->handler()->getErrorMessages());
        $this->assertEquals(['field1' => -1], $form->exportValues());
        $this->assertIsInt($form->exportValues()['field1']);
        $this->assertEquals([['type' => 'number']],
            $form->getDescription()['fields'][0]['rules']);
        
    }
    
    /**
     * Champ number negative Error
     */
    public function testNumberNegativeError() : void
    {
        $html = <<<HTML
<form method="post" action="">
    <input type="number" name="field1" min="0" />
    <button type="submit" name="ok">OK</button>
</form>
HTML;
        $_POST = ['csrf' => 'test', 'ok' => true, 'field1' => '-1'];
        
        $form = $this->buildTestForm($html);
        
        $this->assertFalse($form->validate());
        $this->assertEquals([
            'field1' => [': value must be greater than or equal to 0']
        ], $form->handler()->getErrorMessages());
        $this->assertEmpty($form->exportValues());
        $this->assertEquals([['type' => 'number', 'min' => 0]],
            $form->getDescription()['fields'][0]['rules']);
        
    }
    
    /**
     * Champ number décimal Ok
     */
    public function testNumberDecimalOk() : void
    {
        $html = <<<HTML
<form method="post" action="">
    <input type="number" name="field1" step="any" />
    <button type="submit" name="ok">OK</button>
</form>
HTML;
        $_POST = ['csrf' => 'test', 'ok' => true, 'field1' => '3.14'];
        
        $form = $this->buildTestForm($html);
        
        $this->assertTrue($form->validate());
        $this->assertEmpty($form->handler()->getErrorMessages());
        $this->assertEquals(['field1' => 3.14], $form->exportValues());
        $this->assertIsFloat($form->exportValues()['field1']);
        $this->assertEquals([['type' => 'number', 'step' => 'any']],
            $form->getDescription()['fields'][0]['rules']);
        
    }
    
    /**
     * Champ number décimal Erreur
     */
    public function testNumberDecimalError() : void
    {
        $html = <<<HTML
<form method="post" action="">
    <input type="number" name="field1" />
    <button type="submit" name="ok">OK</button>
</form>
HTML;
        $_POST = ['csrf' => 'test', 'ok' => true, 'field1' => '3.14'];
        
        $form = $this->buildTestForm($html);
        
        $this->assertFalse($form->validate());
        $this->assertEquals([
            'field1' => [': value is not allowed']
        ], $form->handler()->getErrorMessages());
        $this->assertEmpty($form->exportValues());
        $this->assertEquals([['type' => 'number']],
            $form->getDescription()['fields'][0]['rules']);
        
    }
    
    /**
     * Champ number step="10" Ok
     */
    public function testNumberBigStepOk() : void
    {
        $html = <<<HTML
<form method="post" action="">
    <input type="number" name="field1" min="5" step="10" />
    <button type="submit" name="ok">OK</button>
</form>
HTML;
        $_POST = ['csrf' => 'test', 'ok' => true, 'field1' => '2985'];
        
        $form = $this->buildTestForm($html);
        
        $this->assertTrue($form->validate());
        $this->assertEmpty($form->handler()->getErrorMessages());
        $this->assertEquals(['field1' => 2985], $form->exportValues());
        $this->assertIsInt($form->exportValues()['field1']);
        $this->assertEquals([['type' => 'number', 'min' => 5, 'step' => 10]],
            $form->getDescription()['fields'][0]['rules']);
        
    }
    
    /**
     * Champ number step="10" Error
     */
    public function testNumberBigStepError() : void
    {
        $html = <<<HTML
<form method="post" action="">
    <input type="number" name="field1" min="5" step="10" />
    <button type="submit" name="ok">OK</button>
</form>
HTML;
        $_POST = ['csrf' => 'test', 'ok' => true, 'field1' => '2986'];
        
        $form = $this->buildTestForm($html);
        
        $this->assertFalse($form->validate());
        $this->assertEquals([
            'field1' => [': value is not allowed']
        ], $form->handler()->getErrorMessages());
        $this->assertEmpty($form->exportValues());
        $this->assertEquals([['type' => 'number', 'min' => 5, 'step' => 10]],
            $form->getDescription()['fields'][0]['rules']);
        
    }
    
    /**
     * Champ number step="0.01" Ok
     */
    public function testNumberSmallStepOk() : void
    {
        $html = <<<HTML
<form method="post" action="">
    <input type="number" name="field1" step="0.01" />
    <button type="submit" name="ok">OK</button>
</form>
HTML;
        $_POST = ['csrf' => 'test', 'ok' => true, 'field1' => '-3.14'];
        
        $form = $this->buildTestForm($html);
        
        $this->assertTrue($form->validate());
        $this->assertEmpty($form->handler()->getErrorMessages());
        $this->assertEquals(['field1' => -3.14], $form->exportValues());
        $this->assertIsFloat($form->exportValues()['field1']);
        $this->assertEquals([['type' => 'number', 'step' => 0.01]],
            $form->getDescription()['fields'][0]['rules']);
        
    }
    
    /**
     * Champ number step="0.01" Error
     */
    public function testNumberSmallStepError() : void
    {
        $html = <<<HTML
<form method="post" action="">
    <input type="number" name="field1" step="0.01" />
    <button type="submit" name="ok">OK</button>
</form>
HTML;
        $_POST = ['csrf' => 'test', 'ok' => true, 'field1' => '3.141'];
        
        $form = $this->buildTestForm($html);
        
        $this->assertFalse($form->validate());
        $this->assertEquals([
            'field1' => [': value is not allowed']
        ], $form->handler()->getErrorMessages());
        $this->assertEmpty($form->exportValues());
        $this->assertEquals([['type' => 'number', 'step' => 0.01]],
            $form->getDescription()['fields'][0]['rules']);
        
    }
    
}