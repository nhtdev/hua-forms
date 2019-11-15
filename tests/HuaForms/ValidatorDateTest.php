<?php

namespace Tests\HuaForms;

require_once dirname(__FILE__).'/HuaFormsTestCase.php';

class ValidatorDateTest extends \Tests\HuaForms\HuaFormsTestCase
{
    
    /**
     * Champ date OK
     */
    public function testDateOk() : void
    {
        $html = <<<HTML
<form method="post" action="">
    <input type="date" name="field1" />
    <button type="submit" name="ok">OK</button>
</form>
HTML;
        $_POST = ['csrf' => 'test', 'ok' => true, 'field1' => '2019-05-04'];
        
        $form = $this->buildTestForm($html);
        
        $this->assertTrue($form->validate());
        $this->assertEmpty($form->handler()->getErrorMessages());
        $this->assertEquals(['field1' => '2019-05-04'], $form->exportValues());
        $this->assertEquals(['field' => 'field1', 'type' => 'date'], 
            $form->getDescription()['rules'][0]);
        
    }
    
    /**
     * Champ date Erreur (pas de 29 février en 2019)
     */
    public function testDate2902Error() : void
    {
        $html = <<<HTML
<form method="post" action="">
    <input type="date" name="field1" />
    <button type="submit" name="ok">OK</button>
</form>
HTML;
        $_POST = ['csrf' => 'test', 'ok' => true, 'field1' => '2019-02-29'];
        
        $form = $this->buildTestForm($html);
        
        $this->assertFalse($form->validate());
        $this->assertEquals([
            'field1' => ['field1: value is not a valid date']
        ], $form->handler()->getErrorMessages());
        $this->assertEmpty($form->exportValues());
        $this->assertEquals(['field' => 'field1', 'type' => 'date'], 
            $form->getDescription()['rules'][0]);
    }
    
    /**
     * Champ date OK (29 février 2020)
     */
    public function testDate2902Ok() : void
    {
        $html = <<<HTML
<form method="post" action="">
    <input type="date" name="field1" />
    <button type="submit" name="ok">OK</button>
</form>
HTML;
        $_POST = ['csrf' => 'test', 'ok' => true, 'field1' => '2020-02-29'];
        
        $form = $this->buildTestForm($html);
        
        $this->assertTrue($form->validate());
        $this->assertEmpty($form->handler()->getErrorMessages());
        $this->assertEquals(['field1' => '2020-02-29'], $form->exportValues());
        $this->assertEquals(['field' => 'field1', 'type' => 'date'],
            $form->getDescription()['rules'][0]);
        
    }
    
    /**
     * Champ date Erreur
     */
    public function testDateErrorByTag() : void
    {
        $html = <<<HTML
<form method="post" action="">
    <input type="text" date name="field1" min="2019-01-01" max="2019-06-30" />
    <button type="submit" name="ok">OK</button>
</form>
HTML;
        $_POST = ['csrf' => 'test', 'ok' => true, 'field1' => '2019-07-01'];
        
        $form = $this->buildTestForm($html);
        
        $this->assertFalse($form->validate());
        $this->assertEquals([
            'field1' => ['field1: value must be less than or equal to 2019-06-30']
        ], $form->handler()->getErrorMessages());
        $this->assertEmpty($form->exportValues());
        $this->assertEquals(['field' => 'field1', 'type' => 'date', 'min' => '2019-01-01', 'max' => '2019-06-30'],
            $form->getDescription()['rules'][0]);
        
        // Test de rendu du formulaire
        
        $expected = <<<HTML
<form method="post" action="">
<input type="hidden" name="csrf" value="test"/>
<div>field1: value must be less than or equal to 2019-06-30</div>    <input type="text" name="field1" id="field1" value="2019-07-01"/>
    <button type="submit" name="ok" id="ok">OK</button>
</form>
HTML;
        $this->assertSame($expected, $form->render());
    }
    
    /**
     * Champ date Erreur custom
     */
    public function testDateErrorCustom() : void
    {
        $html = <<<HTML
<form method="post" action="">
    <input type="date" name="field1" date-message="Jour invalide" />
    <button type="submit" name="ok">OK</button>
</form>
HTML;
        $_POST = ['csrf' => 'test', 'ok' => true, 'field1' => '2019-01-32'];
        
        $form = $this->buildTestForm($html);
        
        $this->assertFalse($form->validate());
        $this->assertEquals([
            'field1' => ['Jour invalide']
        ], $form->handler()->getErrorMessages());
        $this->assertEmpty($form->exportValues());
        $this->assertEquals(['field' => 'field1', 'type' => 'date', 'message' => 'Jour invalide'],
            $form->getDescription()['rules'][0]);
    }
    
    /**
     * Champ date + min OK
     */
    public function testDateMinOk() : void
    {
        $html = <<<HTML
<form method="post" action="">
    <input type="date" name="field1" min="2019-02-01" max="2019-02-28" />
    <button type="submit" name="ok">OK</button>
</form>
HTML;
        $_POST = ['csrf' => 'test', 'ok' => true, 'field1' => '2019-02-15'];
        
        $form = $this->buildTestForm($html);
        
        $this->assertTrue($form->validate());
        $this->assertEmpty($form->handler()->getErrorMessages());
        $this->assertEquals(['field1' => '2019-02-15'], $form->exportValues());
        $this->assertEquals(['field' => 'field1', 'type' => 'date', 'min' => '2019-02-01', 'max' => '2019-02-28'],
            $form->getDescription()['rules'][0]);
        
    }
    
    /**
     * Champ date + min Error
     */
    public function testDateMinError() : void
    {
        $html = <<<HTML
<form method="post" action="">
    <input type="date" name="field1" min="2019-02-01" max="2019-02-28" />
    <button type="submit" name="ok">OK</button>
</form>
HTML;
        $_POST = ['csrf' => 'test', 'ok' => true, 'field1' => '2018-02-20'];
        
        $form = $this->buildTestForm($html);
        
        $this->assertFalse($form->validate());
        $this->assertEquals([
            'field1' => ['field1: value must be greater than or equal to 2019-02-01']
        ], $form->handler()->getErrorMessages());
        $this->assertEmpty($form->exportValues());
        $this->assertEquals(['field' => 'field1', 'type' => 'date', 'min' => '2019-02-01', 'max' => '2019-02-28'],
            $form->getDescription()['rules'][0]);
        
    }
    
    /**
     * Champ date + min Error
     */
    public function testDateMinErrorCustom() : void
    {
        $html = <<<HTML
<form method="post" action="">
    <input type="date" name="field1" min="2019-02-01" max="2019-02-28" min-message="Après {min}" />
    <button type="submit" name="ok">OK</button>
</form>
HTML;
        $_POST = ['csrf' => 'test', 'ok' => true, 'field1' => '2018-01-30'];
        
        $form = $this->buildTestForm($html);
        
        $this->assertFalse($form->validate());
        $this->assertEquals([
            'field1' => ['Après 2019-02-01']
        ], $form->handler()->getErrorMessages());
        $this->assertEmpty($form->exportValues());
        $this->assertEquals(['field' => 'field1', 'type' => 'date', 'min' => '2019-02-01', 'max' => '2019-02-28', 'min-message' => 'Après {min}'],
            $form->getDescription()['rules'][0]);
        
    }
    
    /**
     * Champ date + max Error
     */
    public function testDateMaxError() : void
    {
        $html = <<<HTML
<form method="post" action="">
    <input type="date" name="field1" min="2019-02-01" max="2019-02-28" />
    <button type="submit" name="ok">OK</button>
</form>
HTML;
        $_POST = ['csrf' => 'test', 'ok' => true, 'field1' => '2020-02-11'];
        
        $form = $this->buildTestForm($html);
        
        $this->assertFalse($form->validate());
        $this->assertEquals([
            'field1' => ['field1: value must be less than or equal to 2019-02-28']
        ], $form->handler()->getErrorMessages());
        $this->assertEmpty($form->exportValues());
        $this->assertEquals(['field' => 'field1', 'type' => 'date', 'min' => '2019-02-01', 'max' => '2019-02-28'],
            $form->getDescription()['rules'][0]);
        
    }
    
    /**
     * Champ date + max Error
     */
    public function testDateMaxErrorCustom() : void
    {
        $html = <<<HTML
<form method="post" action="">
    <input type="date" name="field1" min="2019-02-01" max="2019-02-27" max-message="Avant {max}" />
    <button type="submit" name="ok">OK</button>
</form>
HTML;
        $_POST = ['csrf' => 'test', 'ok' => true, 'field1' => '2019-02-28'];
        
        $form = $this->buildTestForm($html);
        
        $this->assertFalse($form->validate());
        $this->assertEquals([
            'field1' => ['Avant 2019-02-27']
        ], $form->handler()->getErrorMessages());
        $this->assertEmpty($form->exportValues());
        $this->assertEquals(['field' => 'field1', 'type' => 'date', 'min' => '2019-02-01', 'max' => '2019-02-27', 'max-message' => 'Avant {max}'],
            $form->getDescription()['rules'][0]);
        
    }
    
}