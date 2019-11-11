<?php

namespace Tests\HuaForms;

require_once dirname(__FILE__).'/HuaFormsTestCase.php';

class DefaultValueTest extends \Tests\HuaForms\HuaFormsTestCase
{
    
    /**
     * Test des valeurs par défaut
     */
    public function testDefaultValues() : void
    {
        $html = <<<HTML
<form method="post" action="">
    <input type="text" name="field1" id="field1" value="Bonjour" />
    <textarea name="field2" id="field2">Valeur par défaut</textarea>
    <select name="field3" id="field3"> 
        <option value="a">Option A</option>
        <option value="b" selected>Option B</option>
        <option value="c">Option C</option>
    </select>
    <select name="field4[]" id="field4" multiple> 
        <option value="a" selected>Option A</option>
        <option value="b" selected>Option B</option>
        <option value="c">Option C</option>
    </select>
    <input type="checkbox" name="field5" id="field5" checked />
    <input type="checkbox" name="field6" id="field6" value="cochee" checked />
    <input type="checkbox" name="field7" id="field7" />
    <input type="checkbox" name="field8" id="field8" value="cochee" />

    <input type="checkbox" name="field9[]" id="field91" value="1" checked />
    <input type="checkbox" name="field9[]" id="field92" value="2" checked />
    <input type="checkbox" name="field9[]" id="field93" value="3" />

    <button type="submit" name="ok" id="ok">OK</button>
</form>
HTML;
        
        // Test de rendu du formulaire sans submit
        
        $expected = <<<HTML
<form method="post" action="">
<input type="hidden" name="csrf" value="test"/>
    <input type="text" name="field1" id="field1" value="Bonjour"/>
    <textarea name="field2" id="field2">Valeur par d&eacute;faut</textarea>
    <select name="field3" id="field3"> 
        <option value="a">Option A</option>
        <option value="b" selected>Option B</option>
        <option value="c">Option C</option>
    </select>
    <select name="field4[]" id="field4" multiple> 
        <option value="a" selected>Option A</option>
        <option value="b" selected>Option B</option>
        <option value="c">Option C</option>
    </select>
    <input type="checkbox" name="field5" id="field5" checked/>
    <input type="checkbox" name="field6" id="field6" value="cochee" checked/>
    <input type="checkbox" name="field7" id="field7"/>
    <input type="checkbox" name="field8" id="field8" value="cochee"/>

    <input type="checkbox" name="field9[]" id="field91" value="1" checked/>
    <input type="checkbox" name="field9[]" id="field92" value="2" checked/>
    <input type="checkbox" name="field9[]" id="field93" value="3"/>

    <button type="submit" name="ok" id="ok">OK</button>
</form>
HTML;
        $form = $this->buildTestForm($html);
        
        $this->assertEquals(
            [
                'field1' => 'Bonjour',
                'field2' => 'Valeur par défaut',
                'field3' => 'b',
                'field4' => ['a', 'b'],
                'field5' => true,
                'field6' => 'cochee',
                'field9' => ['1', '2']
            ],
            $form->handler()->getDefaultValues());
        
        $this->assertSame($expected, $form->render());
        
        $_POST = [
            'csrf' => 'test', 
            'ok' => true, 
            'field1' => 'Bonsoir', 
            'field2' => 'Valeur modifiée', 
            'field3' => 'c',
            'field4' => ['b', 'c'],
            // field5 = off 
            // field6 = off
            'field7' => 'on',
            'field8' => 'cochee',
            'field9' => ['2', '3']
        ];
        
        $this->assertTrue($form->isSubmitted());
        $this->assertTrue($form->validate());
        $this->assertEmpty($form->handler()->getErrorMessages());
        $this->assertEquals([
            'field1' => 'Bonsoir', 
            'field2' => 'Valeur modifiée', 
            'field3' => 'c', 
            'field4' => ['b', 'c'],
            'field5' => false,
            'field6' => false,
            'field7' => true,
            'field8' => 'cochee',
            'field9' => ['2', '3']
        ], $form->exportValues());
        
        // Test de rendu du formulaire après submit
        
        $expected = <<<HTML
<form method="post" action="">
<input type="hidden" name="csrf" value="test"/>
    <input type="text" name="field1" id="field1" value="Bonsoir"/>
    <textarea name="field2" id="field2">Valeur modifi&eacute;e</textarea>
    <select name="field3" id="field3"> 
        <option value="a">Option A</option>
        <option value="b">Option B</option>
        <option value="c" selected>Option C</option>
    </select>
    <select name="field4[]" id="field4" multiple> 
        <option value="a">Option A</option>
        <option value="b" selected>Option B</option>
        <option value="c" selected>Option C</option>
    </select>
    <input type="checkbox" name="field5" id="field5"/>
    <input type="checkbox" name="field6" id="field6" value="cochee"/>
    <input type="checkbox" name="field7" id="field7" checked/>
    <input type="checkbox" name="field8" id="field8" value="cochee" checked/>

    <input type="checkbox" name="field9[]" id="field91" value="1"/>
    <input type="checkbox" name="field9[]" id="field92" value="2" checked/>
    <input type="checkbox" name="field9[]" id="field93" value="3" checked/>

    <button type="submit" name="ok" id="ok">OK</button>
</form>
HTML;
        $this->assertSame($expected, $form->render());
        
    }
    
}