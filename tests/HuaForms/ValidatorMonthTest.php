<?php

namespace Tests\HuaForms;

require_once dirname(__FILE__).'/HuaFormsTestCase.php';

class ValidatorMonthTest extends \Tests\HuaForms\HuaFormsTestCase
{
    
    /**
     * Champ month OK
     */
    public function testMonthOk() : void
    {
        $html = <<<HTML
<form method="post" action="">
    <input type="month" name="field1" />
    <button type="submit" name="ok">OK</button>
</form>
HTML;
        $_POST = ['csrf' => 'test', 'ok' => true, 'field1' => '2019-12'];
        
        $form = $this->buildTestForm($html);
        
        $this->assertTrue($form->validate());
        $this->assertEmpty($form->handler()->getErrorMessages());
        $this->assertEquals(['field1' => '2019-12'], $form->exportValues());
        $this->assertEquals(['field' => 'field1', 'type' => 'month'], 
            $form->getDescription()['rules'][0]);
        
    }
    
    /**
     * Champ month Erreur
     */
    public function testMonthError() : void
    {
        $html = <<<HTML
<form method="post" action="">
    <input type="month" name="field1" />
    <button type="submit" name="ok">OK</button>
</form>
HTML;
        $_POST = ['csrf' => 'test', 'ok' => true, 'field1' => '2019-13'];
        
        $form = $this->buildTestForm($html);
        
        $this->assertFalse($form->validate());
        $this->assertEquals([
            'field1' => ['field1: value is not a valid month']
        ], $form->handler()->getErrorMessages());
        $this->assertEmpty($form->exportValues());
        $this->assertEquals(['field' => 'field1', 'type' => 'month'], 
            $form->getDescription()['rules'][0]);
    }
    
    /**
     * Champ month Erreur
     */
    public function testMonthErrorByTag() : void
    {
        $html = <<<HTML
<form method="post" action="">
    <input type="text" month name="field1" min="2019-01" max="2019-07" />
    <button type="submit" name="ok">OK</button>
</form>
HTML;
        $_POST = ['csrf' => 'test', 'ok' => true, 'field1' => '2019-08'];
        
        $form = $this->buildTestForm($html);
        
        $this->assertFalse($form->validate());
        $this->assertEquals([
            'field1' => ['field1: value must be less than or equal to 2019-07']
        ], $form->handler()->getErrorMessages());
        $this->assertEmpty($form->exportValues());
        $this->assertEquals(['field' => 'field1', 'type' => 'month', 'min' => '2019-01', 'max' => '2019-07'],
            $form->getDescription()['rules'][0]);
        
        // Test de rendu du formulaire
        
        $expected = <<<HTML
<form method="post" action="">
<input type="hidden" name="csrf" value="test"/>
<div>field1: value must be less than or equal to 2019-07</div>    <input type="text" name="field1" id="field1" value="2019-08"/>
    <button type="submit" name="ok" id="ok">OK</button>
</form>
HTML;
        $this->assertEquals($expected, $form->render());
    }
    
    /**
     * Champ month Erreur custom
     */
    public function testMonthErrorCustom() : void
    {
        $html = <<<HTML
<form method="post" action="">
    <input type="month" name="field1" month-message="Mois invalide" />
    <button type="submit" name="ok">OK</button>
</form>
HTML;
        $_POST = ['csrf' => 'test', 'ok' => true, 'field1' => '1234-ab'];
        
        $form = $this->buildTestForm($html);
        
        $this->assertFalse($form->validate());
        $this->assertEquals([
            'field1' => ['Mois invalide']
        ], $form->handler()->getErrorMessages());
        $this->assertEmpty($form->exportValues());
        $this->assertEquals(['field' => 'field1', 'type' => 'month', 'message' => 'Mois invalide'],
            $form->getDescription()['rules'][0]);
    }
    
    /**
     * Champ month + min OK
     */
    public function testMonthMinOk() : void
    {
        $html = <<<HTML
<form method="post" action="">
    <input type="month" name="field1" min="2019-01" max="2019-12" />
    <button type="submit" name="ok">OK</button>
</form>
HTML;
        $_POST = ['csrf' => 'test', 'ok' => true, 'field1' => '2019-11'];
        
        $form = $this->buildTestForm($html);
        
        $this->assertTrue($form->validate());
        $this->assertEmpty($form->handler()->getErrorMessages());
        $this->assertEquals(['field1' => '2019-11'], $form->exportValues());
        $this->assertEquals(['field' => 'field1', 'type' => 'month', 'min' => '2019-01', 'max' => '2019-12'],
            $form->getDescription()['rules'][0]);
        
    }
    
    /**
     * Champ month + min Error
     */
    public function testMonthMinError() : void
    {
        $html = <<<HTML
<form method="post" action="">
    <input type="month" name="field1" min="2019-01" max="2019-12" />
    <button type="submit" name="ok">OK</button>
</form>
HTML;
        $_POST = ['csrf' => 'test', 'ok' => true, 'field1' => '2018-12'];
        
        $form = $this->buildTestForm($html);
        
        $this->assertFalse($form->validate());
        $this->assertEquals([
            'field1' => ['field1: value must be greater than or equal to 2019-01']
        ], $form->handler()->getErrorMessages());
        $this->assertEmpty($form->exportValues());
        $this->assertEquals(['field' => 'field1', 'type' => 'month', 'min' => '2019-01', 'max' => '2019-12'],
            $form->getDescription()['rules'][0]);
        
    }
    
    /**
     * Champ month + min Error
     */
    public function testMonthMinErrorCustom() : void
    {
        $html = <<<HTML
<form method="post" action="">
    <input type="month" name="field1" min="2019-06" max="2019-12" min-message="Après {min}" />
    <button type="submit" name="ok">OK</button>
</form>
HTML;
        $_POST = ['csrf' => 'test', 'ok' => true, 'field1' => '2019-05'];
        
        $form = $this->buildTestForm($html);
        
        $this->assertFalse($form->validate());
        $this->assertEquals([
            'field1' => ['Après 2019-06']
        ], $form->handler()->getErrorMessages());
        $this->assertEmpty($form->exportValues());
        $this->assertEquals(['field' => 'field1', 'type' => 'month', 'min' => '2019-06', 'max' => '2019-12', 'min-message' => 'Après {min}'],
            $form->getDescription()['rules'][0]);
        
    }
    
    /**
     * Champ month + max Error
     */
    public function testMonthMaxError() : void
    {
        $html = <<<HTML
<form method="post" action="">
    <input type="month" name="field1" min="2019-01" max="2019-12" />
    <button type="submit" name="ok">OK</button>
</form>
HTML;
        $_POST = ['csrf' => 'test', 'ok' => true, 'field1' => '2020-01'];
        
        $form = $this->buildTestForm($html);
        
        $this->assertFalse($form->validate());
        $this->assertEquals([
            'field1' => ['field1: value must be less than or equal to 2019-12']
        ], $form->handler()->getErrorMessages());
        $this->assertEmpty($form->exportValues());
        $this->assertEquals(['field' => 'field1', 'type' => 'month', 'min' => '2019-01', 'max' => '2019-12'],
            $form->getDescription()['rules'][0]);
        
    }
    
    /**
     * Champ month + max Error
     */
    public function testMonthMaxErrorCustom() : void
    {
        $html = <<<HTML
<form method="post" action="">
    <input type="month" name="field1" min="2019-01" max="2019-06" max-message="Avant {max}" />
    <button type="submit" name="ok">OK</button>
</form>
HTML;
        $_POST = ['csrf' => 'test', 'ok' => true, 'field1' => '2019-07'];
        
        $form = $this->buildTestForm($html);
        
        $this->assertFalse($form->validate());
        $this->assertEquals([
            'field1' => ['Avant 2019-06']
        ], $form->handler()->getErrorMessages());
        $this->assertEmpty($form->exportValues());
        $this->assertEquals(['field' => 'field1', 'type' => 'month', 'min' => '2019-01', 'max' => '2019-06', 'max-message' => 'Avant {max}'],
            $form->getDescription()['rules'][0]);
        
    }
    
}