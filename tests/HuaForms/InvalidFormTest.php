<?php

namespace Tests\HuaForms;

require_once dirname(__FILE__).'/HuaFormsTestCase.php';

class InvalidFormTest extends \Tests\HuaForms\HuaFormsTestCase
{
    
    /**
     * Test de validation d'un formulaire simple
     */
    public function testFormSubmit() : void
    {
        $this->expectException(\RuntimeException::class);
        $form = \HuaForms\Factory::form('nonexistingform');
    }
    
}