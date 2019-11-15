<?php

namespace Tests\HuaForms;

require_once dirname(__FILE__).'/HuaFormsTestCase.php';

class ValidatorDateTimeTest extends \Tests\HuaForms\HuaFormsTestCase
{
    
    /**
     * Champ datetime-local OK
     */
    public function testDateTimeOk() : void
    {
        $html = <<<HTML
<form method="post" action="">
    <input type="datetime-local" name="field1" />
    <button type="submit" name="ok">OK</button>
</form>
HTML;
        $_POST = ['csrf' => 'test', 'ok' => true, 'field1' => '2019-11-14T18:15'];
        
        $form = $this->buildTestForm($html);
        
        $this->assertTrue($form->validate());
        $this->assertEmpty($form->handler()->getErrorMessages());
        $this->assertEquals(['field1' => '2019-11-14T18:15'], $form->exportValues());
        $this->assertEquals(['field' => 'field1', 'type' => 'datetime-local'], 
            $form->getDescription()['rules'][0]);
        
    }
    
    /**
     * Champ datetime-local Erreur
     */
    public function testDateTimeError() : void
    {
        $html = <<<HTML
<form method="post" action="">
    <input type="datetime-local" name="field1" />
    <button type="submit" name="ok">OK</button>
</form>
HTML;
        $_POST = ['csrf' => 'test', 'ok' => true, 'field1' => '2019-11-14T25:00'];
        
        $form = $this->buildTestForm($html);
        
        $this->assertFalse($form->validate());
        $this->assertEquals([
            'field1' => ['field1: value is not a valid date']
        ], $form->handler()->getErrorMessages());
        $this->assertEmpty($form->exportValues());
        $this->assertEquals(['field' => 'field1', 'type' => 'datetime-local'], 
            $form->getDescription()['rules'][0]);
    }
    
    /**
     * Champ datetime-local Erreur
     */
    public function testDateTimeErrorByTag() : void
    {
        $html = <<<HTML
<form method="post" action="">
    <input type="text" datetime-local name="field1" min="2019-01-01T10:00" max="2019-02-28T20:00" />
    <button type="submit" name="ok">OK</button>
</form>
HTML;
        $_POST = ['csrf' => 'test', 'ok' => true, 'field1' => '2019-11-14T20:05'];
        
        $form = $this->buildTestForm($html);
        
        $this->assertFalse($form->validate());
        $this->assertEquals([
            'field1' => ['field1: value must be less than or equal to 2019-02-28T20:00']
        ], $form->handler()->getErrorMessages());
        $this->assertEmpty($form->exportValues());
        $this->assertEquals(['field' => 'field1', 'type' => 'datetime-local', 'min' => '2019-01-01T10:00', 'max' => '2019-02-28T20:00'],
            $form->getDescription()['rules'][0]);
        
        // Test de rendu du formulaire
        
        $expected = <<<HTML
<form method="post" action="">
<input type="hidden" name="csrf" value="test"/>
<div>field1: value must be less than or equal to 2019-02-28T20:00</div>    <input type="text" name="field1" id="field1" value="2019-11-14T20:05"/>
    <button type="submit" name="ok" id="ok">OK</button>
</form>
HTML;
        $this->assertEquals($expected, $form->render());
    }
    
    /**
     * Champ datetime-local Erreur custom
     */
    public function testDateTimeErrorCustom() : void
    {
        $html = <<<HTML
<form method="post" action="">
    <input type="datetime-local" name="field1" datetime-local-message="Date invalide" />
    <button type="submit" name="ok">OK</button>
</form>
HTML;
        $_POST = ['csrf' => 'test', 'ok' => true, 'field1' => '2019-02-29T20:05'];
        
        $form = $this->buildTestForm($html);
        
        $this->assertFalse($form->validate());
        $this->assertEquals([
            'field1' => ['Date invalide']
        ], $form->handler()->getErrorMessages());
        $this->assertEmpty($form->exportValues());
        $this->assertEquals(['field' => 'field1', 'type' => 'datetime-local', 'message' => 'Date invalide'],
            $form->getDescription()['rules'][0]);
    }
    
    /**
     * Champ datetime-local + min OK
     */
    public function testDateTimeMinOk() : void
    {
        $html = <<<HTML
<form method="post" action="">
    <input type="datetime-local" name="field1" min="2019-01-01T20:05" max="2019-02-28T20:05" />
    <button type="submit" name="ok">OK</button>
</form>
HTML;
        $_POST = ['csrf' => 'test', 'ok' => true, 'field1' => '2019-01-14T20:05'];
        
        $form = $this->buildTestForm($html);
        
        $this->assertTrue($form->validate());
        $this->assertEmpty($form->handler()->getErrorMessages());
        $this->assertEquals(['field1' => '2019-01-14T20:05'], $form->exportValues());
        $this->assertEquals(['field' => 'field1', 'type' => 'datetime-local', 'min' => '2019-01-01T20:05', 'max' => '2019-02-28T20:05'],
            $form->getDescription()['rules'][0]);
        
    }
    
    /**
     * Champ datetime-local + min Error
     */
    public function testDateTimeMinError() : void
    {
        $html = <<<HTML
<form method="post" action="">
    <input type="datetime-local" name="field1" min="2019-01-02T20:05" max="2019-02-28T20:05" />
    <button type="submit" name="ok">OK</button>
</form>
HTML;
        $_POST = ['csrf' => 'test', 'ok' => true, 'field1' => '2019-01-01T20:05'];
        
        $form = $this->buildTestForm($html);
        
        $this->assertFalse($form->validate());
        $this->assertEquals([
            'field1' => ['field1: value must be greater than or equal to 2019-01-02T20:05']
        ], $form->handler()->getErrorMessages());
        $this->assertEmpty($form->exportValues());
        $this->assertEquals(['field' => 'field1', 'type' => 'datetime-local', 'min' => '2019-01-02T20:05', 'max' => '2019-02-28T20:05'],
            $form->getDescription()['rules'][0]);
        
    }
    
    /**
     * Champ datetime-local + min Error
     */
    public function testDateTimeMinErrorCustom() : void
    {
        $html = <<<HTML
<form method="post" action="">
    <input type="datetime-local" name="field1" min="2019-01-02T20:05" max="2019-02-28T20:05" min-message="Après {min}" />
    <button type="submit" name="ok">OK</button>
</form>
HTML;
        $_POST = ['csrf' => 'test', 'ok' => true, 'field1' => '2019-01-02T20:00'];
        
        $form = $this->buildTestForm($html);
        
        $this->assertFalse($form->validate());
        $this->assertEquals([
            'field1' => ['Après 2019-01-02T20:05']
        ], $form->handler()->getErrorMessages());
        $this->assertEmpty($form->exportValues());
        $this->assertEquals(['field' => 'field1', 'type' => 'datetime-local', 'min' => '2019-01-02T20:05', 'max' => '2019-02-28T20:05', 'min-message' => 'Après {min}'],
            $form->getDescription()['rules'][0]);
        
    }
    
    /**
     * Champ datetime-local + max Error
     */
    public function testDateTimeMaxError() : void
    {
        $html = <<<HTML
<form method="post" action="">
    <input type="datetime-local" name="field1" min="2019-01-02T20:05" max="2019-02-28T20:05" />
    <button type="submit" name="ok">OK</button>
</form>
HTML;
        $_POST = ['csrf' => 'test', 'ok' => true, 'field1' => '2019-02-28T20:06'];
        
        $form = $this->buildTestForm($html);
        
        $this->assertFalse($form->validate());
        $this->assertEquals([
            'field1' => ['field1: value must be less than or equal to 2019-02-28T20:05']
        ], $form->handler()->getErrorMessages());
        $this->assertEmpty($form->exportValues());
        $this->assertEquals(['field' => 'field1', 'type' => 'datetime-local', 'min' => '2019-01-02T20:05', 'max' => '2019-02-28T20:05'],
            $form->getDescription()['rules'][0]);
        
    }
    
    /**
     * Champ datetime-local + max Error
     */
    public function testDateTimeMaxErrorCustom() : void
    {
        $html = <<<HTML
<form method="post" action="">
    <input type="datetime-local" name="field1" min="2019-01-02T20:05" max="2019-02-28T20:05" max-message="Avant {max}" />
    <button type="submit" name="ok">OK</button>
</form>
HTML;
        $_POST = ['csrf' => 'test', 'ok' => true, 'field1' => '2019-03-02T23:59'];
        
        $form = $this->buildTestForm($html);
        
        $this->assertFalse($form->validate());
        $this->assertEquals([
            'field1' => ['Avant 2019-02-28T20:05']
        ], $form->handler()->getErrorMessages());
        $this->assertEmpty($form->exportValues());
        $this->assertEquals(['field' => 'field1', 'type' => 'datetime-local', 'min' => '2019-01-02T20:05', 'max' => '2019-02-28T20:05', 'max-message' => 'Avant {max}'],
            $form->getDescription()['rules'][0]);
        
    }
    
    /**
     * Champ datetime-local + secondes OK
     */
    public function testDateTimeWithSecondsOk() : void
    {
        $html = <<<HTML
<form method="post" action="">
    <input type="datetime-local" name="field1" step="10" />
    <button type="submit" name="ok">OK</button>
</form>
HTML;
        $_POST = ['csrf' => 'test', 'ok' => true, 'field1' => '2019-03-02T12:12:30'];
        
        $form = $this->buildTestForm($html);
        
        $this->assertTrue($form->validate());
        $this->assertEmpty($form->handler()->getErrorMessages());
        $this->assertEquals(['field1' => '2019-03-02T12:12:30'], $form->exportValues());
        $this->assertEquals(['field' => 'field1', 'type' => 'datetime-local', 'step' => 10],
            $form->getDescription()['rules'][0]);
        
    }
    
    /**
     * Champ datetime-local + secondes Error
     */
    public function testDateTimeWithSecondsError() : void
    {
        $html = <<<HTML
<form method="post" action="">
    <input type="datetime-local" name="field1" step="60" />
    <button type="submit" name="ok">OK</button>
</form>
HTML;
        $_POST = ['csrf' => 'test', 'ok' => true, 'field1' => '2019-03-02T12:12:30'];
        
        $form = $this->buildTestForm($html);
        
        $this->assertFalse($form->validate());
        $this->assertEquals([
            'field1' => ['field1: value is not a valid date']
        ], $form->handler()->getErrorMessages());
        $this->assertEmpty($form->exportValues());
        $this->assertEquals(['field' => 'field1', 'type' => 'datetime-local', 'step' => 60],
            $form->getDescription()['rules'][0]);
        
    }
    
    /**
     * Champ datetime-local + secondes + step Ok
     */
    public function testStepSecondsOk() : void
    {
        $html = <<<HTML
<form method="post" action="">
    <input type="datetime-local" name="field1" step="10" />
    <button type="submit" name="ok">OK</button>
</form>
HTML;
        $_POST = ['csrf' => 'test', 'ok' => true, 'field1' => '2019-03-02T12:12:30'];
        
        $form = $this->buildTestForm($html);
        
        $this->assertTrue($form->validate());
        $this->assertEmpty($form->handler()->getErrorMessages());
        $this->assertEquals(['field1' => '2019-03-02T12:12:30'], $form->exportValues());
        $this->assertEquals(['field' => 'field1', 'type' => 'datetime-local', 'step' => 10],
            $form->getDescription()['rules'][0]);
        
    }
    
    /**
     * Champ datetime-local + secondes + step Error
     */
    public function testStepSecondsError() : void
    {
        $html = <<<HTML
<form method="post" action="">
    <input type="datetime-local" name="field1" step="10" />
    <button type="submit" name="ok">OK</button>
</form>
HTML;
        $_POST = ['csrf' => 'test', 'ok' => true, 'field1' => '2019-03-02T12:12:35'];
        
        $form = $this->buildTestForm($html);
        
        $this->assertFalse($form->validate());
        $this->assertEquals([
            'field1' => ['field1: value is not allowed']
        ], $form->handler()->getErrorMessages());
        $this->assertEmpty($form->exportValues());
        $this->assertEquals(['field' => 'field1', 'type' => 'datetime-local', 'step' => 10],
            $form->getDescription()['rules'][0]);
        
    }
    
    /**
     * Champ datetime-local + 2 minutes step Ok
     */
    public function testStepMinutesOk() : void
    {
        $html = <<<HTML
<form method="post" action="">
    <input type="datetime-local" name="field1" step="120" />
    <button type="submit" name="ok">OK</button>
</form>
HTML;
        $_POST = ['csrf' => 'test', 'ok' => true, 'field1' => '2019-03-02T12:12'];
        
        $form = $this->buildTestForm($html);
        
        $this->assertTrue($form->validate());
        $this->assertEmpty($form->handler()->getErrorMessages());
        $this->assertEquals(['field1' => '2019-03-02T12:12'], $form->exportValues());
        $this->assertEquals(['field' => 'field1', 'type' => 'datetime-local', 'step' => 120],
            $form->getDescription()['rules'][0]);
        
    }
    
    /**
     * Champ datetime-local + 2 minutes step Error
     */
    public function testStepMinutesError() : void
    {
        $html = <<<HTML
<form method="post" action="">
    <input type="datetime-local" name="field1" step="120" />
    <button type="submit" name="ok">OK</button>
</form>
HTML;
        $_POST = ['csrf' => 'test', 'ok' => true, 'field1' => '2019-03-02T12:13'];
        
        $form = $this->buildTestForm($html);
        
        $this->assertFalse($form->validate());
        $this->assertEquals([
            'field1' => ['field1: value is not allowed']
        ], $form->handler()->getErrorMessages());
        $this->assertEmpty($form->exportValues());
        $this->assertEquals(['field' => 'field1', 'type' => 'datetime-local', 'step' => 120],
            $form->getDescription()['rules'][0]);
        
    }
    
    /**
     * Champ datetime-local + 1 hour step Ok
     */
    public function testStepHourOk() : void
    {
        $html = <<<HTML
<form method="post" action="">
    <input type="datetime-local" name="field1" step="3600" />
    <button type="submit" name="ok">OK</button>
</form>
HTML;
        $_POST = ['csrf' => 'test', 'ok' => true, 'field1' => '2019-03-02T12:00'];
        
        $form = $this->buildTestForm($html);
        
        $this->assertTrue($form->validate());
        $this->assertEmpty($form->handler()->getErrorMessages());
        $this->assertEquals(['field1' => '2019-03-02T12:00'], $form->exportValues());
        $this->assertEquals(['field' => 'field1', 'type' => 'datetime-local', 'step' => 3600],
            $form->getDescription()['rules'][0]);
        
    }
    
    /**
     * Champ datetime-local + 1 hour step Error
     */
    public function testStepHourError() : void
    {
        $html = <<<HTML
<form method="post" action="">
    <input type="datetime-local" name="field1" step="3600" />
    <button type="submit" name="ok">OK</button>
</form>
HTML;
        $_POST = ['csrf' => 'test', 'ok' => true, 'field1' => '2019-03-02T12:30'];
        
        $form = $this->buildTestForm($html);
        
        $this->assertFalse($form->validate());
        $this->assertEquals([
            'field1' => ['field1: value is not allowed']
        ], $form->handler()->getErrorMessages());
        $this->assertEmpty($form->exportValues());
        $this->assertEquals(['field' => 'field1', 'type' => 'datetime-local', 'step' => 3600],
            $form->getDescription()['rules'][0]);
        
    }
    
}