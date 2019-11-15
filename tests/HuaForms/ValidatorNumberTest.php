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
        $this->assertEquals(['field' => 'field1', 'type' => 'number'], 
            $form->getDescription()['rules'][0]);
        
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
            'field1' => ['field1: value is not a valid number']
        ], $form->handler()->getErrorMessages());
        $this->assertEmpty($form->exportValues());
        $this->assertEquals(['field' => 'field1', 'type' => 'number'], 
            $form->getDescription()['rules'][0]);
    }
    
    /**
     * Champ number Erreur custom
     */
    public function testNumberErrorCustom() : void
    {
        $html = <<<HTML
<form method="post" action="">
    <input type="number" name="field1" number-message="Nombre invalide" />
    <button type="submit" name="ok">OK</button>
</form>
HTML;
        $_POST = ['csrf' => 'test', 'ok' => true, 'field1' => 'test'];
        
        $form = $this->buildTestForm($html);
        
        $this->assertFalse($form->validate());
        $this->assertEquals([
            'field1' => ['Nombre invalide']
        ], $form->handler()->getErrorMessages());
        $this->assertEmpty($form->exportValues());
        $this->assertEquals(['field' => 'field1', 'type' => 'number', 'message' => 'Nombre invalide'],
            $form->getDescription()['rules'][0]);
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
        $this->assertEquals(['field' => 'field1', 'type' => 'number', 'min' => 10, 'max' => 20],
            $form->getDescription()['rules'][0]);
        
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
            'field1' => ['field1: value must be greater than or equal to 10']
        ], $form->handler()->getErrorMessages());
        $this->assertEmpty($form->exportValues());
        $this->assertEquals(['field' => 'field1', 'type' => 'number', 'min' => 10, 'max' => 20],
            $form->getDescription()['rules'][0]);
        
    }
    
    /**
     * Champ number + min Error
     */
    public function testNumberMinErrorCustom() : void
    {
        $html = <<<HTML
<form method="post" action="">
    <input type="number" name="field1" min="10" max="20" min-message="Doit être supérieur ou égal à {min}" />
    <button type="submit" name="ok">OK</button>
</form>
HTML;
        $_POST = ['csrf' => 'test', 'ok' => true, 'field1' => '4'];
        
        $form = $this->buildTestForm($html);
        
        $this->assertFalse($form->validate());
        $this->assertEquals([
            'field1' => ['Doit être supérieur ou égal à 10']
        ], $form->handler()->getErrorMessages());
        $this->assertEmpty($form->exportValues());
        $this->assertEquals(['field' => 'field1', 'type' => 'number', 'min' => 10, 'max' => 20, 'min-message' => 'Doit être supérieur ou égal à {min}'],
            $form->getDescription()['rules'][0]);
        
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
            'field1' => ['field1: value must be less than or equal to 20']
        ], $form->handler()->getErrorMessages());
        $this->assertEmpty($form->exportValues());
        $this->assertEquals(['field' => 'field1', 'type' => 'number', 'min' => 10, 'max' => 20],
            $form->getDescription()['rules'][0]);
        
    }
    
    /**
     * Champ number + max Error
     */
    public function testNumberMaxErrorCustom() : void
    {
        $html = <<<HTML
<form method="post" action="">
    <input type="number" name="field1" min="10" max="20" max-message="Doit être inférieur ou égal à {max}" />
    <button type="submit" name="ok">OK</button>
</form>
HTML;
        $_POST = ['csrf' => 'test', 'ok' => true, 'field1' => '24'];
        
        $form = $this->buildTestForm($html);
        
        $this->assertFalse($form->validate());
        $this->assertEquals([
            'field1' => ['Doit être inférieur ou égal à 20']
        ], $form->handler()->getErrorMessages());
        $this->assertEmpty($form->exportValues());
        $this->assertEquals(['field' => 'field1', 'type' => 'number', 'min' => 10, 'max' => 20, 'max-message' => 'Doit être inférieur ou égal à {max}'],
            $form->getDescription()['rules'][0]);
        
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
        $this->assertEquals(['field' => 'field1', 'type' => 'number'],
            $form->getDescription()['rules'][0]);
        
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
            'field1' => ['field1: value must be greater than or equal to 0']
        ], $form->handler()->getErrorMessages());
        $this->assertEmpty($form->exportValues());
        $this->assertEquals(['field' => 'field1', 'type' => 'number', 'min' => 0],
            $form->getDescription()['rules'][0]);
        
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
        $this->assertEquals(['field' => 'field1', 'type' => 'number', 'step' => 'any'],
            $form->getDescription()['rules'][0]);
        
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
            'field1' => ['field1: value is not allowed']
        ], $form->handler()->getErrorMessages());
        $this->assertEmpty($form->exportValues());
        $this->assertEquals(['field' => 'field1', 'type' => 'number'],
            $form->getDescription()['rules'][0]);
        
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
        $this->assertEquals(['field' => 'field1', 'type' => 'number', 'min' => 5, 'step' => 10],
            $form->getDescription()['rules'][0]);
        
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
            'field1' => ['field1: value is not allowed']
        ], $form->handler()->getErrorMessages());
        $this->assertEmpty($form->exportValues());
        $this->assertEquals(['field' => 'field1', 'type' => 'number', 'min' => 5, 'step' => 10],
            $form->getDescription()['rules'][0]);
        
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
        $this->assertEquals(['field' => 'field1', 'type' => 'number', 'step' => 0.01],
            $form->getDescription()['rules'][0]);
        
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
            'field1' => ['field1: value is not allowed']
        ], $form->handler()->getErrorMessages());
        $this->assertEmpty($form->exportValues());
        $this->assertEquals(['field' => 'field1', 'type' => 'number', 'step' => 0.01],
            $form->getDescription()['rules'][0]);
        
    }
    
    /**
     * Champ number step="0.01" Error
     */
    public function testNumberSmallStepErrorCustom() : void
    {
        $html = <<<HTML
<form method="post" action="">
    <input type="number" name="field1" step="0.01" step-message="Maximum 2 chiffres après la virgule" />
    <button type="submit" name="ok">OK</button>
</form>
HTML;
        $_POST = ['csrf' => 'test', 'ok' => true, 'field1' => '3.141'];
        
        $form = $this->buildTestForm($html);
        $this->assertFalse($form->validate());
        $this->assertEquals([
            'field1' => ['Maximum 2 chiffres après la virgule']
        ], $form->handler()->getErrorMessages());
        $this->assertEmpty($form->exportValues());
        $this->assertEquals(['field' => 'field1', 'type' => 'number', 'step' => 0.01, 'step-message' => 'Maximum 2 chiffres après la virgule'],
            $form->getDescription()['rules'][0]);
        
    }
    
    /**
     * Champ texte avec attribut number
     */
    public function testTextNumberError() : void
    {
        $html = <<<HTML
<form method="post" action="">
    <input type="text" name="field1" number min="5" max="10" step="2" />
    <button type="submit" name="ok">OK</button>
</form>
HTML;
        $_POST = ['csrf' => 'test', 'ok' => true, 'field1' => '6'];
        
        $form = $this->buildTestForm($html);
        
        $this->assertFalse($form->validate());
        $this->assertEquals([
            'field1' => ['field1: value is not allowed']
        ], $form->handler()->getErrorMessages());
        $this->assertEmpty($form->exportValues());
        $this->assertEquals(['field' => 'field1', 'type' => 'number', 'min' => 5, 'max' => 10, 'step' => 2],
            $form->getDescription()['rules'][0]);
        
        // Test de rendu du formulaire
        
        $expected = <<<HTML
<form method="post" action="">
<input type="hidden" name="csrf" value="test"/>
<div>field1: value is not allowed</div>    <input type="text" name="field1" id="field1" value="6"/>
    <button type="submit" name="ok" id="ok">OK</button>
</form>
HTML;
        $this->assertSame($expected, $form->render());
    }
    
    /**
     * Champ range OK
     */
    public function testRangeOk() : void
    {
        $html = <<<HTML
<form method="post" action="">
    <input type="range" name="field1" min="10" max="20" />
    <button type="submit" name="ok">OK</button>
</form>
HTML;
        $_POST = ['csrf' => 'test', 'ok' => true, 'field1' => '14'];
        
        $form = $this->buildTestForm($html);
        
        $this->assertTrue($form->validate());
        $this->assertEmpty($form->handler()->getErrorMessages());
        $this->assertEquals(['field1' => '14'], $form->exportValues());
        $this->assertIsInt($form->exportValues()['field1']);
        $this->assertEquals(['field' => 'field1', 'type' => 'number', 'min' => 10, 'max' => 20],
            $form->getDescription()['rules'][0]);
        
    }
    
    /**
     * Champ range Error
     */
    public function testRangeError() : void
    {
        $html = <<<HTML
<form method="post" action="">
    <input type="range" name="field1" min="10" max="20" />
    <button type="submit" name="ok">OK</button>
</form>
HTML;
        $_POST = ['csrf' => 'test', 'ok' => true, 'field1' => '4'];
        
        $form = $this->buildTestForm($html);
        
        $this->assertFalse($form->validate());
        $this->assertEquals([
            'field1' => ['field1: value must be greater than or equal to 10']
        ], $form->handler()->getErrorMessages());
        $this->assertEmpty($form->exportValues());
        $this->assertEquals(['field' => 'field1', 'type' => 'number', 'min' => 10, 'max' => 20],
            $form->getDescription()['rules'][0]);
        
    }
    
    /**
     * Champ range OK
     */
    public function testRangeDefaultOk() : void
    {
        $html = <<<HTML
<form method="post" action="">
    <input type="range" name="field1" />
    <button type="submit" name="ok">OK</button>
</form>
HTML;
        $_POST = ['csrf' => 'test', 'ok' => true, 'field1' => '60'];
        
        $form = $this->buildTestForm($html);
        
        $this->assertTrue($form->validate());
        $this->assertEmpty($form->handler()->getErrorMessages());
        $this->assertEquals(['field1' => 60], $form->exportValues());
        $this->assertIsInt($form->exportValues()['field1']);
        $this->assertEquals(['field' => 'field1', 'type' => 'number', 'min' => 0, 'max' => 100],
            $form->getDescription()['rules'][0]);
        
    }
    
    /**
     * Champ range Error avec options par défaut
     */
    public function testRangeDefaultError() : void
    {
        $html = <<<HTML
<form method="post" action="">
    <input type="range" name="field1" />
    <button type="submit" name="ok">OK</button>
</form>
HTML;
        $_POST = ['csrf' => 'test', 'ok' => true, 'field1' => '105'];
        
        $form = $this->buildTestForm($html);
        
        $this->assertFalse($form->validate());
        $this->assertEquals([
            'field1' => ['field1: value must be less than or equal to 100']
        ], $form->handler()->getErrorMessages());
        $this->assertEmpty($form->exportValues());
        $this->assertEquals(['field' => 'field1', 'type' => 'number', 'min' => 0, 'max' => 100],
            $form->getDescription()['rules'][0]);
        
    }
    
    /**
     * Champ range OK avec min seulement
     */
    public function testRangeMinOnlyOk() : void
    {
        $html = <<<HTML
<form method="post" action="">
    <input type="range" name="field1" min="0" step="0.01" />
    <button type="submit" name="ok">OK</button>
</form>
HTML;
        $_POST = ['csrf' => 'test', 'ok' => true, 'field1' => '65.28'];
        
        $form = $this->buildTestForm($html);
        $this->assertTrue($form->validate());
        $this->assertEmpty($form->handler()->getErrorMessages());
        $this->assertEquals(['field1' => 65.28], $form->exportValues());
        $this->assertIsFloat($form->exportValues()['field1']);
        $this->assertEquals(['field' => 'field1', 'type' => 'number', 'min' => 0, 'max' => 100, 'step' => 0.01],
            $form->getDescription()['rules'][0]);
        
    }
    
}