<?php

namespace Tests\HuaForms;

use PHPUnit\Framework\TestCase;

class HuaFormsTestCase extends TestCase
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
        $form->handler()->setCsrf('csrf', 'test');
        return $form;
    }
    
    protected function setUp() : void
    {
        $_GET = [];
        $_POST = [];
        $_FILES = [];
    }
    
    public static function setUpBeforeClass() : void
    {
        foreach (glob('tests/tmpforms/*.form.html') as $file) {
            unlink($file);
        }
        foreach (glob('tests/tmpforms/built/*') as $file) {
            unlink($file);
        }
        foreach (glob('/tmp/upload_*') as $file) {
            unlink($file);
        }
    }
}