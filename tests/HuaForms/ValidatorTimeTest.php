<?php

namespace Tests\HuaForms;

require_once dirname(__FILE__).'/HuaFormsTestCase.php';

class ValidatorTimeTest extends \Tests\HuaForms\HuaFormsTestCase
{
    
    /**
     * Champ time OK
     */
    public function testTimeOk() : void
    {
        $html = <<<HTML
<form method="post" action="">
    <input type="time" name="field1" />
    <button type="submit" name="ok">OK</button>
</form>
HTML;
        $_POST = ['csrf' => 'test', 'ok' => true, 'field1' => '18:15'];
        
        $form = $this->buildTestForm($html);
        
        $this->assertTrue($form->validate());
        $this->assertEmpty($form->handler()->getErrorMessages());
        $this->assertEquals(['field1' => '18:15'], $form->exportValues());
        $this->assertEquals(['field' => 'field1', 'type' => 'time'], 
            $form->getDescription()['rules'][0]);
        
    }
    
    /**
     * Champ time Erreur
     */
    public function testTimeError() : void
    {
        $html = <<<HTML
<form method="post" action="">
    <input type="time" name="field1" />
    <button type="submit" name="ok">OK</button>
</form>
HTML;
        $_POST = ['csrf' => 'test', 'ok' => true, 'field1' => '25:00'];
        
        $form = $this->buildTestForm($html);
        
        $this->assertFalse($form->validate());
        $this->assertEquals([
            'field1' => ['field1: value is not a valid time']
        ], $form->handler()->getErrorMessages());
        $this->assertEmpty($form->exportValues());
        $this->assertEquals(['field' => 'field1', 'type' => 'time'], 
            $form->getDescription()['rules'][0]);
    }
    
    /**
     * Champ time Erreur
     */
    public function testTimeErrorByTag() : void
    {
        $html = <<<HTML
<form method="post" action="">
    <input type="text" time name="field1" min="10:00" max="20:00" />
    <button type="submit" name="ok">OK</button>
</form>
HTML;
        $_POST = ['csrf' => 'test', 'ok' => true, 'field1' => '20:05'];
        
        $form = $this->buildTestForm($html);
        
        $this->assertFalse($form->validate());
        $this->assertEquals([
            'field1' => ['field1: value must be less than or equal to 20:00']
        ], $form->handler()->getErrorMessages());
        $this->assertEmpty($form->exportValues());
        $this->assertEquals(['field' => 'field1', 'type' => 'time', 'min' => '10:00', 'max' => '20:00'],
            $form->getDescription()['rules'][0]);
        
        // Test de rendu du formulaire
        
        $expected = <<<HTML
<form method="post" action="">
<input type="hidden" name="csrf" value="test"/>
<div>field1: value must be less than or equal to 20:00</div>    <input type="text" name="field1" id="field1" value="20:05"/>
    <button type="submit" name="ok" id="ok">OK</button>
</form>
HTML;
        $this->assertEquals($expected, $form->render());
    }
    
    /**
     * Champ time Erreur custom
     */
    public function testTimeErrorCustom() : void
    {
        $html = <<<HTML
<form method="post" action="">
    <input type="time" name="field1" time-message="Heure invalide" />
    <button type="submit" name="ok">OK</button>
</form>
HTML;
        $_POST = ['csrf' => 'test', 'ok' => true, 'field1' => 'hh:00'];
        
        $form = $this->buildTestForm($html);
        
        $this->assertFalse($form->validate());
        $this->assertEquals([
            'field1' => ['Heure invalide']
        ], $form->handler()->getErrorMessages());
        $this->assertEmpty($form->exportValues());
        $this->assertEquals(['field' => 'field1', 'type' => 'time', 'message' => 'Heure invalide'],
            $form->getDescription()['rules'][0]);
    }
    
    /**
     * Champ time + min OK
     */
    public function testTimeMinOk() : void
    {
        $html = <<<HTML
<form method="post" action="">
    <input type="time" name="field1" min="10:00" max="20:00" />
    <button type="submit" name="ok">OK</button>
</form>
HTML;
        $_POST = ['csrf' => 'test', 'ok' => true, 'field1' => '11:59'];
        
        $form = $this->buildTestForm($html);
        
        $this->assertTrue($form->validate());
        $this->assertEmpty($form->handler()->getErrorMessages());
        $this->assertEquals(['field1' => '11:59'], $form->exportValues());
        $this->assertEquals(['field' => 'field1', 'type' => 'time', 'min' => '10:00', 'max' => '20:00'],
            $form->getDescription()['rules'][0]);
        
    }
    
    /**
     * Champ time + min Error
     */
    public function testTimeMinError() : void
    {
        $html = <<<HTML
<form method="post" action="">
    <input type="time" name="field1" min="10:00" max="20:00" />
    <button type="submit" name="ok">OK</button>
</form>
HTML;
        $_POST = ['csrf' => 'test', 'ok' => true, 'field1' => '09:59'];
        
        $form = $this->buildTestForm($html);
        
        $this->assertFalse($form->validate());
        $this->assertEquals([
            'field1' => ['field1: value must be greater than or equal to 10:00']
        ], $form->handler()->getErrorMessages());
        $this->assertEmpty($form->exportValues());
        $this->assertEquals(['field' => 'field1', 'type' => 'time', 'min' => '10:00', 'max' => '20:00'],
            $form->getDescription()['rules'][0]);
        
    }
    
    /**
     * Champ time + min Error
     */
    public function testTimeMinErrorCustom() : void
    {
        $html = <<<HTML
<form method="post" action="">
    <input type="time" name="field1" min="10:00" max="20:00" min-message="Après {min}" />
    <button type="submit" name="ok">OK</button>
</form>
HTML;
        $_POST = ['csrf' => 'test', 'ok' => true, 'field1' => '09:59'];
        
        $form = $this->buildTestForm($html);
        
        $this->assertFalse($form->validate());
        $this->assertEquals([
            'field1' => ['Après 10:00']
        ], $form->handler()->getErrorMessages());
        $this->assertEmpty($form->exportValues());
        $this->assertEquals(['field' => 'field1', 'type' => 'time', 'min' => '10:00', 'max' => '20:00', 'min-message' => 'Après {min}'],
            $form->getDescription()['rules'][0]);
        
    }
    
    /**
     * Champ time inversé (min > max) OK
     */
    public function testTimeInverseOk1() : void
    {
        $html = <<<HTML
<form method="post" action="">
    <input type="time" name="field1" min="20:00" max="10:00" />
    <button type="submit" name="ok">OK</button>
</form>
HTML;
        $_POST = ['csrf' => 'test', 'ok' => true, 'field1' => '06:30'];
        
        $form = $this->buildTestForm($html);
        
        $this->assertTrue($form->validate());
        $this->assertEmpty($form->handler()->getErrorMessages());
        $this->assertEquals(['field1' => '06:30'], $form->exportValues());
        $this->assertEquals(['field' => 'field1', 'type' => 'time', 'min' => '20:00', 'max' => '10:00'],
            $form->getDescription()['rules'][0]);
        
    }
    
    /**
     * Champ time inversé (min > max) OK
     */
    public function testTimeInverseOk2() : void
    {
        $html = <<<HTML
<form method="post" action="">
    <input type="time" name="field1" min="20:00" max="10:00" />
    <button type="submit" name="ok">OK</button>
</form>
HTML;
        $_POST = ['csrf' => 'test', 'ok' => true, 'field1' => '21:30'];
        
        $form = $this->buildTestForm($html);
        
        $this->assertTrue($form->validate());
        $this->assertEmpty($form->handler()->getErrorMessages());
        $this->assertEquals(['field1' => '21:30'], $form->exportValues());
        $this->assertEquals(['field' => 'field1', 'type' => 'time', 'min' => '20:00', 'max' => '10:00'],
            $form->getDescription()['rules'][0]);
        
    }
    
    /**
     * Champ time inversé (min > max) Error
     */
    public function testTimeMinInverseError() : void
    {
        $html = <<<HTML
<form method="post" action="">
    <input type="time" name="field1" min="20:00" max="10:00" />
    <button type="submit" name="ok">OK</button>
</form>
HTML;
        $_POST = ['csrf' => 'test', 'ok' => true, 'field1' => '11:30'];
        
        $form = $this->buildTestForm($html);
        
        $this->assertFalse($form->validate());
        $this->assertEquals([
            'field1' => ['field1: value must not be between 10:00 and 20:00']
        ], $form->handler()->getErrorMessages());
        $this->assertEmpty($form->exportValues());
        $this->assertEquals(['field' => 'field1', 'type' => 'time', 'min' => '20:00', 'max' => '10:00'],
            $form->getDescription()['rules'][0]);
        
    }
    
    /**
     * Champ time + max Error
     */
    public function testTimeMaxError() : void
    {
        $html = <<<HTML
<form method="post" action="">
    <input type="time" name="field1" min="10:00" max="20:00" />
    <button type="submit" name="ok">OK</button>
</form>
HTML;
        $_POST = ['csrf' => 'test', 'ok' => true, 'field1' => '20:01'];
        
        $form = $this->buildTestForm($html);
        
        $this->assertFalse($form->validate());
        $this->assertEquals([
            'field1' => ['field1: value must be less than or equal to 20:00']
        ], $form->handler()->getErrorMessages());
        $this->assertEmpty($form->exportValues());
        $this->assertEquals(['field' => 'field1', 'type' => 'time', 'min' => '10:00', 'max' => '20:00'],
            $form->getDescription()['rules'][0]);
        
    }
    
    /**
     * Champ time + max Error
     */
    public function testTimeMaxErrorCustom() : void
    {
        $html = <<<HTML
<form method="post" action="">
    <input type="time" name="field1" min="10:00" max="20:00" max-message="Avant {max}" />
    <button type="submit" name="ok">OK</button>
</form>
HTML;
        $_POST = ['csrf' => 'test', 'ok' => true, 'field1' => '23:59'];
        
        $form = $this->buildTestForm($html);
        
        $this->assertFalse($form->validate());
        $this->assertEquals([
            'field1' => ['Avant 20:00']
        ], $form->handler()->getErrorMessages());
        $this->assertEmpty($form->exportValues());
        $this->assertEquals(['field' => 'field1', 'type' => 'time', 'min' => '10:00', 'max' => '20:00', 'max-message' => 'Avant {max}'],
            $form->getDescription()['rules'][0]);
        
    }
    
    /**
     * Champ time + secondes OK
     */
    public function testTimeWithSecondsOk() : void
    {
        $html = <<<HTML
<form method="post" action="">
    <input type="time" name="field1" step="10" />
    <button type="submit" name="ok">OK</button>
</form>
HTML;
        $_POST = ['csrf' => 'test', 'ok' => true, 'field1' => '12:12:30'];
        
        $form = $this->buildTestForm($html);
        
        $this->assertTrue($form->validate());
        $this->assertEmpty($form->handler()->getErrorMessages());
        $this->assertEquals(['field1' => '12:12:30'], $form->exportValues());
        $this->assertEquals(['field' => 'field1', 'type' => 'time', 'step' => 10],
            $form->getDescription()['rules'][0]);
        
    }
    
    /**
     * Champ time + secondes Error
     */
    public function testTimeWithSecondsError() : void
    {
        $html = <<<HTML
<form method="post" action="">
    <input type="time" name="field1" step="60" />
    <button type="submit" name="ok">OK</button>
</form>
HTML;
        $_POST = ['csrf' => 'test', 'ok' => true, 'field1' => '12:12:30'];
        
        $form = $this->buildTestForm($html);
        
        $this->assertFalse($form->validate());
        $this->assertEquals([
            'field1' => ['field1: value is not a valid time']
        ], $form->handler()->getErrorMessages());
        $this->assertEmpty($form->exportValues());
        $this->assertEquals(['field' => 'field1', 'type' => 'time', 'step' => 60],
            $form->getDescription()['rules'][0]);
        
    }
    
    /**
     * Champ time + secondes + step Ok
     */
    public function testStepSecondsOk() : void
    {
        $html = <<<HTML
<form method="post" action="">
    <input type="time" name="field1" step="10" />
    <button type="submit" name="ok">OK</button>
</form>
HTML;
        $_POST = ['csrf' => 'test', 'ok' => true, 'field1' => '12:12:30'];
        
        $form = $this->buildTestForm($html);
        
        $this->assertTrue($form->validate());
        $this->assertEmpty($form->handler()->getErrorMessages());
        $this->assertEquals(['field1' => '12:12:30'], $form->exportValues());
        $this->assertEquals(['field' => 'field1', 'type' => 'time', 'step' => 10],
            $form->getDescription()['rules'][0]);
        
    }
    
    /**
     * Champ time + secondes + step Error
     */
    public function testStepSecondsError() : void
    {
        $html = <<<HTML
<form method="post" action="">
    <input type="time" name="field1" step="10" />
    <button type="submit" name="ok">OK</button>
</form>
HTML;
        $_POST = ['csrf' => 'test', 'ok' => true, 'field1' => '12:12:35'];
        
        $form = $this->buildTestForm($html);
        
        $this->assertFalse($form->validate());
        $this->assertEquals([
            'field1' => ['field1: value is not allowed']
        ], $form->handler()->getErrorMessages());
        $this->assertEmpty($form->exportValues());
        $this->assertEquals(['field' => 'field1', 'type' => 'time', 'step' => 10],
            $form->getDescription()['rules'][0]);
        
    }
    
    /**
     * Champ time + 2 minutes step Ok
     */
    public function testStepMinutesOk() : void
    {
        $html = <<<HTML
<form method="post" action="">
    <input type="time" name="field1" step="120" />
    <button type="submit" name="ok">OK</button>
</form>
HTML;
        $_POST = ['csrf' => 'test', 'ok' => true, 'field1' => '12:12'];
        
        $form = $this->buildTestForm($html);
        
        $this->assertTrue($form->validate());
        $this->assertEmpty($form->handler()->getErrorMessages());
        $this->assertEquals(['field1' => '12:12'], $form->exportValues());
        $this->assertEquals(['field' => 'field1', 'type' => 'time', 'step' => 120],
            $form->getDescription()['rules'][0]);
        
    }
    
    /**
     * Champ time + 2 minutes step Error
     */
    public function testStepMinutesError() : void
    {
        $html = <<<HTML
<form method="post" action="">
    <input type="time" name="field1" step="120" />
    <button type="submit" name="ok">OK</button>
</form>
HTML;
        $_POST = ['csrf' => 'test', 'ok' => true, 'field1' => '12:13'];
        
        $form = $this->buildTestForm($html);
        
        $this->assertFalse($form->validate());
        $this->assertEquals([
            'field1' => ['field1: value is not allowed']
        ], $form->handler()->getErrorMessages());
        $this->assertEmpty($form->exportValues());
        $this->assertEquals(['field' => 'field1', 'type' => 'time', 'step' => 120],
            $form->getDescription()['rules'][0]);
        
    }
    
    /**
     * Champ time + 1 hour step Ok
     */
    public function testStepHourOk() : void
    {
        $html = <<<HTML
<form method="post" action="">
    <input type="time" name="field1" step="3600" />
    <button type="submit" name="ok">OK</button>
</form>
HTML;
        $_POST = ['csrf' => 'test', 'ok' => true, 'field1' => '12:00'];
        
        $form = $this->buildTestForm($html);
        
        $this->assertTrue($form->validate());
        $this->assertEmpty($form->handler()->getErrorMessages());
        $this->assertEquals(['field1' => '12:00'], $form->exportValues());
        $this->assertEquals(['field' => 'field1', 'type' => 'time', 'step' => 3600],
            $form->getDescription()['rules'][0]);
        
    }
    
    /**
     * Champ time + 1 hour step Error
     */
    public function testStepHourError() : void
    {
        $html = <<<HTML
<form method="post" action="">
    <input type="time" name="field1" step="3600" />
    <button type="submit" name="ok">OK</button>
</form>
HTML;
        $_POST = ['csrf' => 'test', 'ok' => true, 'field1' => '12:30'];
        
        $form = $this->buildTestForm($html);
        
        $this->assertFalse($form->validate());
        $this->assertEquals([
            'field1' => ['field1: value is not allowed']
        ], $form->handler()->getErrorMessages());
        $this->assertEmpty($form->exportValues());
        $this->assertEquals(['field' => 'field1', 'type' => 'time', 'step' => 3600],
            $form->getDescription()['rules'][0]);
        
    }
    
}