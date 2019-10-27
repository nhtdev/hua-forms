<?php

namespace Tests\HuaForms;

use PHPUnit\Framework\TestCase;

class ParserTest extends TestCase
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
    
}