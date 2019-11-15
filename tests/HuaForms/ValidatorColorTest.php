<?php

namespace Tests\HuaForms;

require_once dirname(__FILE__).'/HuaFormsTestCase.php';

class ValidatorColorTest extends \Tests\HuaForms\HuaFormsTestCase
{
    
    /**
     * Champ texte avec règle color OK
     */
    public function testTextColorOk() : void
    {
        $html = <<<HTML
<form method="post" action="">
    <input type="text" color name="field1" />
    <button type="submit" name="ok">OK</button>
</form>
HTML;
        $_POST = ['csrf' => 'test', 'ok' => true, 'field1' => '#0169ef'];
        
        $form = $this->buildTestForm($html);
        
        $this->assertTrue($form->validate());
        $this->assertEmpty($form->handler()->getErrorMessages());
        $this->assertEquals(['field1' => '#0169ef'], $form->exportValues());
        $this->assertEquals(['field' => 'field1', 'type' => 'color'], 
            $form->getDescription()['rules'][0]);
        
        // Test de rendu du formulaire
        
        $expected = <<<HTML
<form method="post" action="">
<input type="hidden" name="csrf" value="test"/>
    <input type="text" name="field1" id="field1" value="#0169ef"/>
    <button type="submit" name="ok" id="ok">OK</button>
</form>
HTML;
        $this->assertSame($expected, $form->render());
    }
    
    /**
     * Champ texte avec règle color Erreur
     */
    public function testTextColorError() : void
    {
        $html = <<<HTML
<form method="post" action="">
    <input type="text" color name="field1" />
    <button type="submit" name="ok">OK</button>
</form>
HTML;
        $_POST = ['csrf' => 'test', 'ok' => true, 'field1' => '#0123fg'];
        
        $form = $this->buildTestForm($html);
        
        $this->assertFalse($form->validate());
        $this->assertEquals([
            'field1' => ['field1: invalid color']
        ], $form->handler()->getErrorMessages());
        $this->assertEmpty($form->exportValues());
        $this->assertEquals(['field' => 'field1', 'type' => 'color'], 
            $form->getDescription()['rules'][0]);
    }
    
    /**
     * Champ color OK
     */
    public function testColorOk() : void
    {
        $html = <<<HTML
<form method="post" action="">
    <input type="color" name="field1" />
    <button type="submit" name="ok">OK</button>
</form>
HTML;
        $_POST = ['csrf' => 'test', 'ok' => true, 'field1' => '#0123ef'];
        
        $form = $this->buildTestForm($html);
        
        $this->assertTrue($form->validate());
        $this->assertEmpty($form->handler()->getErrorMessages());
        $this->assertEquals(['field1' => '#0123ef'], $form->exportValues());
        $this->assertEquals(['field' => 'field1', 'type' => 'color'],
            $form->getDescription()['rules'][0]);
        
    }
    
    /**
     * Champ texte avec règle color Erreur
     */
    public function testColorError() : void
    {
        $html = <<<HTML
<form method="post" action="">
    <input type="color" name="field1" />
    <button type="submit" name="ok">OK</button>
</form>
HTML;
        $_POST = ['csrf' => 'test', 'ok' => true, 'field1' => '#1234567'];
        
        $form = $this->buildTestForm($html);
        
        $this->assertFalse($form->validate());
        $this->assertEquals([
            'field1' => ['field1: invalid color']
        ], $form->handler()->getErrorMessages());
        $this->assertEmpty($form->exportValues());
        $this->assertEquals(['field' => 'field1', 'type' => 'color'],
            $form->getDescription()['rules'][0]);
    }
    
}