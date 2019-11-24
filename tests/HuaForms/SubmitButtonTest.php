<?php

namespace Tests\HuaForms;

require_once dirname(__FILE__).'/HuaFormsTestCase.php';

class SubmitButtonTest extends \Tests\HuaForms\HuaFormsTestCase
{
    
    /**
     * Test submit un seul bouton
     */
    public function testSubmitOneButton() : void
    {
        $html = <<<HTML
<form method="post" action="">
    <input type="text" name="field1" id="field1" value=""/>
    <button type="submit" name="btn1" id="btn1">OK</button>
</form>
HTML;
        
        $_POST = ['csrf' => 'test', 'field1' => 'Test', 'btn1' => "OK"];
        $form = $this->buildTestForm($html);
        
        $this->assertTrue($form->isSubmitted());
        $this->assertEquals('btn1', $form->handler()->getSubmittedButton());
        
    }
    
    /**
     * Test submit plusieurs boutons
     */
    public function testSubmitManyButtons() : void
    {
        $html = <<<HTML
<form method="post" action="">
    <input type="text" name="field1" id="field1" value=""/>
    <button type="submit" name="btn1" id="btn1">OK 1</button>
    <button type="submit" name="btn2" id="btn2">OK 2</button>
    <button type="submit" name="btn3" id="btn3">OK 3</button>
</form>
HTML;
        
        $_POST = ['csrf' => 'test', 'field1' => 'Test', 'btn2' => "OK"];
        $form = $this->buildTestForm($html);
        
        $this->assertTrue($form->isSubmitted());
        $this->assertEquals('btn2', $form->handler()->getSubmittedButton());
        
    }
    
    /**
     * Test submit plusieurs boutons => échec
     */
    public function testSubmitManyButtonsFailed() : void
    {
        $html = <<<HTML
<form method="post" action="">
    <input type="text" name="field1" id="field1" value=""/>
    <button type="submit" name="btn1" id="btn1">OK 1</button>
    <button type="submit" name="btn2" id="btn2">OK 2</button>
    <button type="submit" name="btn3" id="btn3">OK 3</button>
</form>
HTML;
        
        $_POST = ['csrf' => 'test', 'field1' => 'Test', 'btn4' => "OK"];
        $form = $this->buildTestForm($html);
        
        $this->assertFalse($form->isSubmitted());
        $this->assertNull($form->handler()->getSubmittedButton());
        
    }
    
    /**
     * Test submit bouton sans attribut name
     */
    public function testSubmitButtonNoName() : void
    {
        $html = <<<HTML
<form method="post" action="">
    <input type="text" name="field1" id="field1" value=""/>
    <button type="submit">OK</button>
</form>
HTML;
        
        $_POST = ['csrf' => 'test', 'field1' => 'Test', 'submit' => true];
        $form = $this->buildTestForm($html);
        
        $this->assertTrue($form->isSubmitted());
        $this->assertEquals('submit', $form->handler()->getSubmittedButton());
        
        // Test de rendu du formulaire
        
        $expected = <<<HTML
<form method="post" action="">
<input type="hidden" name="csrf" value="test"/>
    <input type="text" name="field1" id="field1" value=""/>
    <button type="submit" name="submit" id="submit">OK</button>
</form>
HTML;
        $this->assertEquals($expected, $form->render());
        
    }
    
    /**
     * Test submit plusieurs boutons sans attributs name
     */
    public function testSubmitManyButtonsNoName() : void
    {
        $html = <<<HTML
<form method="post" action="">
    <input type="text" name="field1" id="field1" value=""/>
    <button type="submit">OK 1</button>
    <button type="submit">OK 2</button>
    <button type="submit">OK 3</button>
</form>
HTML;
        
        $_POST = ['csrf' => 'test', 'field1' => 'Test', 'submit2' => "OK"];
        $form = $this->buildTestForm($html);
        
        $this->assertTrue($form->isSubmitted());
        $this->assertEquals('submit2', $form->handler()->getSubmittedButton());
        
        // Test de rendu du formulaire
        
        $expected = <<<HTML
<form method="post" action="">
<input type="hidden" name="csrf" value="test"/>
    <input type="text" name="field1" id="field1" value=""/>
    <button type="submit" name="submit" id="submit">OK 1</button>
    <button type="submit" name="submit2" id="submit2">OK 2</button>
    <button type="submit" name="submit3" id="submit3">OK 3</button>
</form>
HTML;
        $this->assertEquals($expected, $form->render());
        
    }
    
    
    /**
     * Test submit plusieurs boutons => échec sauf si force submit
     */
    public function testSubmitForceSubmit() : void
    {
        $html = <<<HTML
<form method="post" action="">
    <input type="text" name="field1" id="field1" value=""/>
    <button type="submit" name="btn1" id="btn1">OK 1</button>
    <button type="submit" name="btn2" id="btn2">OK 2</button>
    <button type="submit" name="btn3" id="btn3">OK 3</button>
</form>
HTML;
        
        $_POST = ['csrf' => 'test', 'field1' => 'Test'];
        $form = $this->buildTestForm($html);
        
        $this->assertFalse($form->isSubmitted());
        $this->assertNull($form->handler()->getSubmittedButton());
        
        $form->handler()->forceSubmit('btn2');
        
        $this->assertTrue($form->isSubmitted());
        $this->assertEquals('btn2', $form->handler()->getSubmittedButton());
        
    }
    
    /**
     * Test submit plusieurs boutons => échec sauf si force submit
     */
    public function testSubmitForceSubmit2() : void
    {
        $html = <<<HTML
<form method="post" action="">
    <input type="text" name="field1" id="field1" value=""/>
    <button type="submit" name="btn1" id="btn1">OK 1</button>
    <button type="submit" name="btn2" id="btn2">OK 2</button>
    <button type="submit" name="btn3" id="btn3">OK 3</button>
</form>
HTML;
        
        $_POST = ['csrf' => 'test', 'field1' => 'Test'];
        $form = $this->buildTestForm($html);
        
        $this->assertFalse($form->isSubmitted());
        $this->assertNull($form->handler()->getSubmittedButton());
        
        $form->handler()->forceSubmit();
        
        $this->assertTrue($form->isSubmitted());
        $this->assertEquals('btn1', $form->handler()->getSubmittedButton());
        
    }
    
    /**
     * Test submit plusieurs boutons => échec sauf si force submit
     */
    public function testSubmitForceSubmitInvalid() : void
    {
        $html = <<<HTML
<form method="post" action="">
    <input type="text" name="field1" id="field1" value=""/>
    <button type="submit" name="btn1" id="btn1">OK 1</button>
    <button type="submit" name="btn2" id="btn2">OK 2</button>
    <button type="submit" name="btn3" id="btn3">OK 3</button>
</form>
HTML;
        
        $_POST = ['csrf' => 'test', 'field1' => 'Test'];
        $form = $this->buildTestForm($html);
        
        $this->assertFalse($form->isSubmitted());
        $this->assertNull($form->handler()->getSubmittedButton());
        
        $this->expectException(\RuntimeException::class);
        $form->handler()->forceSubmit('btn4');
        
    }
    
}