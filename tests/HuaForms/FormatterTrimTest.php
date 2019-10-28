<?php

namespace Tests\HuaForms;

require_once dirname(__FILE__).'/HuaFormsTestCase.php';

class FormatterTrimTest extends \Tests\HuaForms\HuaFormsTestCase
{
    
    /**
     * Test de la fonction trim sur un champ texte
     */
    public function testTrim() : void
    {
        $html = <<<HTML
<form method="post" action="">
    <input type="text" name="field1" required/>
    <input type="text" name="field2" required trim/>
    <button type="submit" name="ok">OK</button>
</form>
HTML;
        $_POST = ['csrf' => 'test', 'ok' => true, 'field1' => ' Value1 ', 'field2' => ' Value2 '];
        
        $form = $this->buildTestForm($html);
        
        $this->assertEquals($form->isSubmitted(), true);
        $this->assertEquals($form->validate(), true);
        $this->assertEmpty($form->handler()->getErrorMessages());
        $this->assertEquals(['field1' => ' Value1 ', 'field2' => 'Value2'], $form->exportValues());
        $this->assertEquals([['type' => 'trim']],
            $form->getDescription()['fields'][1]['formatters']);
        
    }
    
    /**
     * Fonction trim sur un champ obligatoire
     */
    public function testRequiredField() : void
    {
        $html = <<<HTML
<form method="post" action="">
    <div form-errors class="errors"></div>
    <input type="text" name="field1" required trim/>
    <button type="submit" name="ok">OK</button>
</form>
HTML;
        $_POST = ['csrf' => 'test', 'ok' => true, 'field1' => '     '];
        
        $form = $this->buildTestForm($html);
        
        $this->assertEquals($form->isSubmitted(), true);
        $this->assertEquals($form->validate(), false);
        $this->assertEquals([
            'field1' => [': field is required']
        ], $form->handler()->getErrorMessages());
        $this->assertEmpty($form->exportValues());
        $this->assertEquals([['type' => 'trim']],
            $form->getDescription()['fields'][0]['formatters']);
        
        // Test de rendu du formulaire
        
        $expected = <<<HTML
<form method="post" action="">
<input type="hidden" name="csrf" value="test"/>
   <div class="errors">: field is required</div>    <input type="text" name="field1" required id="field1" value="     "/>
    <button type="submit" name="ok" id="ok">OK</button>
</form>
HTML;
        $this->assertSame($expected, $form->render());
    }
    
}