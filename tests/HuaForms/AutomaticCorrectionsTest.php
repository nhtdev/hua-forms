<?php

namespace Tests\HuaForms;

require_once dirname(__FILE__).'/HuaFormsTestCase.php';

class AutomaticCorrectionsTest extends \Tests\HuaForms\HuaFormsTestCase
{
    
    /**
     * Ajoute au type "text" aux champs input sans type
     * Ajoute automatiquement un attribut "id" correspondant au "name"
     * Remplit la valeur des champs textes
     */
    public function testAddTypeToInput() : void
    {
        $html = <<<HTML
<form method="post" action="">
    <input name="field" />
</form>
HTML;
        
        $form = $this->buildTestForm($html);
        $form->setDefaults(['field' => 'fieldvalue']);
        
        $expected = <<<HTML
<form method="post" action="">
<input type="hidden" name="csrf" value="test"/>
    <input name="field" type="text" id="field" value="fieldvalue"/>
</form>
HTML;
        $this->assertEquals($expected, $form->render());
        
    }
    
    /**
     * Ajoute automatiquement un attribut "for" à l'élément <label>
     */
    public function testAddForInLabelNodes() : void
    {
        $html = <<<HTML
<form method="post" action="">
    <div>
        <label>Test</label>
        <input type="text" name="field" id="field-id" value=""/>
    </div>
    <div>
        <input type="text" name="field2" id="field-id2" value=""/>
        <label>Test 2</label>
    </div>
</form>
HTML;
        
        $form = $this->buildTestForm($html);
        
        $expected = <<<HTML
<form method="post" action="">
<input type="hidden" name="csrf" value="test"/>
    <div>
        <label for="field-id">Test</label>
        <input type="text" name="field" id="field-id" value=""/>
    </div>
    <div>
        <input type="text" name="field2" id="field-id2" value=""/>
        <label for="field-id2">Test 2</label>
    </div>
</form>
HTML;
        $this->assertEquals($expected, $form->render());
        
    }
    
    /**
     * Remplit la valeur d'un champ <select>
     */
    public function testSetSelected() : void
    {
        $html = <<<HTML
<form method="post" action="">
    <div>
        <select name="field" id="field-id">
            <option value="a">Option A</option>
            <option value="b">Option b</option>
        </select>
    </div>
</form>
HTML;
        
        $form = $this->buildTestForm($html);
        $form->setDefaults(['field' => 'b']);
        
        $expected = <<<HTML
<form method="post" action="">
<input type="hidden" name="csrf" value="test"/>
    <div>
        <select name="field" id="field-id">
            <option value="a">Option A</option>
            <option value="b" selected>Option b</option>
        </select>
    </div>
</form>
HTML;
        $this->assertEquals($expected, $form->render());
        
    }
    
    /**
     * Le name d'un champ <select multiple> doit se terminer par "[]"
     */
    public function testFixSelectMultipleName() : void
    {
        $html = <<<HTML
<form method="post" action="">
    <div>
        <select name="field" id="field-id" multiple>
            <option value="a">Option A</option>
            <option value="b">Option b</option>
        </select>
    </div>
</form>
HTML;
        
        $form = $this->buildTestForm($html);
        $form->setDefaults(['field' => ['a', 'b']]);
        
        $expected = <<<HTML
<form method="post" action="">
<input type="hidden" name="csrf" value="test"/>
    <div>
        <select name="field[]" id="field-id" multiple>
            <option value="a" selected>Option A</option>
            <option value="b" selected>Option b</option>
        </select>
    </div>
</form>
HTML;
        $this->assertEquals($expected, $form->render());
        
    }
    
    /**
     * Définit automatiquement l'attribut "enctype" du formulaire s'il contient au moins un champ de type fichier
     */
    public function testEncTypeIfFileInput() : void
    {
        $html = <<<HTML
<form method="post" action="">
    <input type="file" name="file" />
</form>
HTML;
        
        $form = $this->buildTestForm($html);
        
        $expected = <<<HTML
<form method="post" action="" enctype="multipart/form-data">
<input type="hidden" name="csrf" value="test"/>
    <input type="file" name="file" id="file"/>
</form>
HTML;
        $this->assertEquals($expected, $form->render());
        
    }
    
    /**
     * Add a "name" attribute to any submit button
     */
    public function testAddNameToSubmits() : void
    {
        $html = <<<HTML
<form method="post" action="">
    <button type="submit">Test 1</button>
    <button type="submit">Test 2</button>
</form>
HTML;
        
        $form = $this->buildTestForm($html);
        
        $expected = <<<HTML
<form method="post" action="">
<input type="hidden" name="csrf" value="test"/>
    <button type="submit" name="submit" id="submit">Test 1</button>
    <button type="submit" name="submit2" id="submit2">Test 2</button>
</form>
HTML;
        $this->assertEquals($expected, $form->render());
        
    }
    
    /**
     * Convert <input> type "submit", "button", "reset" to <button>
     */
    public function testConvertToButton() : void
    {
        $html = <<<HTML
<form method="post" action="">
    <input type="submit" name="submit-btn1"/>
    <input type="submit" value="Submit" name="submit-btn2"/>
    <input type="button"/>
    <input type="button" value="Button"/>
    <input type="reset"/>
    <input type="reset" value="Reset 2"/>
</form>
HTML;
        
        $form = $this->buildTestForm($html);
        
        $expected = <<<HTML
<form method="post" action="">
<input type="hidden" name="csrf" value="test"/>
    <button type="submit" name="submit-btn1" id="submit-btn1">OK</button>
    <button type="submit" name="submit-btn2" id="submit-btn2">Submit</button>
    <button type="button"> </button>
    <button type="button">Button</button>
    <button type="reset">Reset</button>
    <button type="reset">Reset 2</button>
</form>
HTML;
        $this->assertEquals($expected, $form->render());
        
    }
    
    /**
     * Add a "type=button" attribute to any button without "type" attribute
     */
    public function testAddTypeToButton() : void
    {
        $html = <<<HTML
<form method="post" action="">
    <button>Test</button>
</form>
HTML;
        
        $form = $this->buildTestForm($html);
        
        $expected = <<<HTML
<form method="post" action="">
<input type="hidden" name="csrf" value="test"/>
    <button type="button">Test</button>
</form>
HTML;
        $this->assertEquals($expected, $form->render());
        
    }
    
}