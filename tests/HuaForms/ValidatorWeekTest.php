<?php

namespace Tests\HuaForms;

require_once dirname(__FILE__).'/HuaFormsTestCase.php';

class ValidatorWeekTest extends \Tests\HuaForms\HuaFormsTestCase
{
    
    /**
     * Champ week OK
     */
    public function testWeekOk() : void
    {
        $html = <<<HTML
<form method="post" action="">
    <input type="week" name="field1" />
    <button type="submit" name="ok">OK</button>
</form>
HTML;
        $_POST = ['csrf' => 'test', 'ok' => true, 'field1' => '2019-W40'];
        
        $form = $this->buildTestForm($html);
        
        $this->assertTrue($form->validate());
        $this->assertEmpty($form->handler()->getErrorMessages());
        $this->assertEquals(['field1' => '2019-W40'], $form->exportValues());
        $this->assertEquals([['type' => 'week']], 
            $form->getDescription()['fields'][0]['rules']);
        
    }
    
    /**
     * Champ week Erreur (52 semaines seulement en 2019)
     */
    public function testWeek53Error() : void
    {
        $html = <<<HTML
<form method="post" action="">
    <input type="week" name="field1" />
    <button type="submit" name="ok">OK</button>
</form>
HTML;
        $_POST = ['csrf' => 'test', 'ok' => true, 'field1' => '2019-W53'];
        
        $form = $this->buildTestForm($html);
        
        $this->assertFalse($form->validate());
        $this->assertEquals([
            'field1' => [': value is not a valid week number']
        ], $form->handler()->getErrorMessages());
        $this->assertEmpty($form->exportValues());
        $this->assertEquals([['type' => 'week']], 
            $form->getDescription()['fields'][0]['rules']);
    }
    
    /**
     * Champ week OK (53 semaines en 2015)
     */
    public function testWeek53Ok() : void
    {
        $html = <<<HTML
<form method="post" action="">
    <input type="week" name="field1" />
    <button type="submit" name="ok">OK</button>
</form>
HTML;
        $_POST = ['csrf' => 'test', 'ok' => true, 'field1' => '2015-W53'];
        
        $form = $this->buildTestForm($html);
        
        $this->assertTrue($form->validate());
        $this->assertEmpty($form->handler()->getErrorMessages());
        $this->assertEquals(['field1' => '2015-W53'], $form->exportValues());
        $this->assertEquals([['type' => 'week']],
            $form->getDescription()['fields'][0]['rules']);
        
    }
    
    /**
     * Champ week Erreur
     */
    public function testWeekErrorByTag() : void
    {
        $html = <<<HTML
<form method="post" action="">
    <input type="text" week name="field1" min="2019-W20" max="2019-W30" />
    <button type="submit" name="ok">OK</button>
</form>
HTML;
        $_POST = ['csrf' => 'test', 'ok' => true, 'field1' => '2019-W31'];
        
        $form = $this->buildTestForm($html);
        
        $this->assertFalse($form->validate());
        $this->assertEquals([
            'field1' => [': value must be less than or equal to 2019-W30']
        ], $form->handler()->getErrorMessages());
        $this->assertEmpty($form->exportValues());
        $this->assertEquals([['type' => 'week', 'min' => '2019-W20', 'max' => '2019-W30']],
            $form->getDescription()['fields'][0]['rules']);
        
        // Test de rendu du formulaire
        
        $expected = <<<HTML
<form method="post" action="">
<input type="hidden" name="csrf" value="test"/>
<div>: value must be less than or equal to 2019-W30</div>    <input type="text" name="field1" id="field1" value="2019-W31"/>
    <button type="submit" name="ok" id="ok">OK</button>
</form>
HTML;
        $this->assertSame($expected, $form->render());
    }
    
    /**
     * Champ week Erreur custom
     */
    public function testWeekErrorCustom() : void
    {
        $html = <<<HTML
<form method="post" action="">
    <input type="week" name="field1" week-message="Semaine invalide" />
    <button type="submit" name="ok">OK</button>
</form>
HTML;
        $_POST = ['csrf' => 'test', 'ok' => true, 'field1' => '1234-Wab'];
        
        $form = $this->buildTestForm($html);
        
        $this->assertFalse($form->validate());
        $this->assertEquals([
            'field1' => ['Semaine invalide']
        ], $form->handler()->getErrorMessages());
        $this->assertEmpty($form->exportValues());
        $this->assertEquals([['type' => 'week', 'message' => 'Semaine invalide']],
            $form->getDescription()['fields'][0]['rules']);
    }
    
    /**
     * Champ week + min OK
     */
    public function testWeekMinOk() : void
    {
        $html = <<<HTML
<form method="post" action="">
    <input type="week" name="field1" min="2019-W20" max="2019-W30" />
    <button type="submit" name="ok">OK</button>
</form>
HTML;
        $_POST = ['csrf' => 'test', 'ok' => true, 'field1' => '2019-W20'];
        
        $form = $this->buildTestForm($html);
        
        $this->assertTrue($form->validate());
        $this->assertEmpty($form->handler()->getErrorMessages());
        $this->assertEquals(['field1' => '2019-W20'], $form->exportValues());
        $this->assertEquals([['type' => 'week', 'min' => '2019-W20', 'max' => '2019-W30']],
            $form->getDescription()['fields'][0]['rules']);
        
    }
    
    /**
     * Champ week + min Error
     */
    public function testWeekMinError() : void
    {
        $html = <<<HTML
<form method="post" action="">
    <input type="week" name="field1" min="2019-W20" max="2019-W30" />
    <button type="submit" name="ok">OK</button>
</form>
HTML;
        $_POST = ['csrf' => 'test', 'ok' => true, 'field1' => '2018-W19'];
        
        $form = $this->buildTestForm($html);
        
        $this->assertFalse($form->validate());
        $this->assertEquals([
            'field1' => [': value must be greater than or equal to 2019-W20']
        ], $form->handler()->getErrorMessages());
        $this->assertEmpty($form->exportValues());
        $this->assertEquals([['type' => 'week', 'min' => '2019-W20', 'max' => '2019-W30']],
            $form->getDescription()['fields'][0]['rules']);
        
    }
    
    /**
     * Champ week + min Error
     */
    public function testWeekMinErrorCustom() : void
    {
        $html = <<<HTML
<form method="post" action="">
    <input type="week" name="field1" min="2019-W20" max="2019-W30" min-message="Après {min}" />
    <button type="submit" name="ok">OK</button>
</form>
HTML;
        $_POST = ['csrf' => 'test', 'ok' => true, 'field1' => '2018-W25'];
        
        $form = $this->buildTestForm($html);
        
        $this->assertFalse($form->validate());
        $this->assertEquals([
            'field1' => ['Après 2019-W20']
        ], $form->handler()->getErrorMessages());
        $this->assertEmpty($form->exportValues());
        $this->assertEquals([['type' => 'week', 'min' => '2019-W20', 'max' => '2019-W30', 'min-message' => 'Après {min}']],
            $form->getDescription()['fields'][0]['rules']);
        
    }
    
    /**
     * Champ week + max Error
     */
    public function testWeekMaxError() : void
    {
        $html = <<<HTML
<form method="post" action="">
    <input type="week" name="field1" min="2019-W20" max="2019-W30" />
    <button type="submit" name="ok">OK</button>
</form>
HTML;
        $_POST = ['csrf' => 'test', 'ok' => true, 'field1' => '2020-W31'];
        
        $form = $this->buildTestForm($html);
        
        $this->assertFalse($form->validate());
        $this->assertEquals([
            'field1' => [': value must be less than or equal to 2019-W30']
        ], $form->handler()->getErrorMessages());
        $this->assertEmpty($form->exportValues());
        $this->assertEquals([['type' => 'week', 'min' => '2019-W20', 'max' => '2019-W30']],
            $form->getDescription()['fields'][0]['rules']);
        
    }
    
    /**
     * Champ week + max Error
     */
    public function testWeekMaxErrorCustom() : void
    {
        $html = <<<HTML
<form method="post" action="">
    <input type="week" name="field1" min="2019-W20" max="2019-W30" max-message="Avant {max}" />
    <button type="submit" name="ok">OK</button>
</form>
HTML;
        $_POST = ['csrf' => 'test', 'ok' => true, 'field1' => '2019-W31'];
        
        $form = $this->buildTestForm($html);
        
        $this->assertFalse($form->validate());
        $this->assertEquals([
            'field1' => ['Avant 2019-W30']
        ], $form->handler()->getErrorMessages());
        $this->assertEmpty($form->exportValues());
        $this->assertEquals([['type' => 'week', 'min' => '2019-W20', 'max' => '2019-W30', 'max-message' => 'Avant {max}']],
            $form->getDescription()['fields'][0]['rules']);
        
    }
    
}