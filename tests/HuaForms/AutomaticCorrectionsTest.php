<?php

namespace Tests\HuaForms;

use PHPUnit\Framework\TestCase;

class AutomaticCorrectionsTest extends TestCase
{
    protected function buildTestForm(string $html) : \HuaForms\Facade
    {
        $tmpdir = 'tests/tmpforms/';
        if (!is_dir($tmpdir)) {
            mkdir($tmpdir);
        }
        $cachedir = $tmpdir . 'built/';
        if (!is_dir($cachedir)) {
            mkdir($cachedir);
        }
        \HuaForms\Factory::setOptions(['formPath' => $tmpdir, 'cache' => false, 'csrfClass' => \HuaForms\Csrf\UnitTest::class]);
        $formId = uniqid('form_');
        file_put_contents($tmpdir.$formId.'.form.html', $html);
        $form = \HuaForms\Factory::form($formId);
        $form->renderer()->setCsrf('csrf', 'test');
        return $form;
    }
    
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
        $this->assertSame($expected, $form->render());
        
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
</form>
HTML;
        $this->assertSame($expected, $form->render());
        
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
        $this->assertSame($expected, $form->render());
        
    }
    
    
    
}