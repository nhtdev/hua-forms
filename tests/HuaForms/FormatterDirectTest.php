<?php

namespace Tests\HuaForms;

require_once dirname(__FILE__).'/HuaFormsTestCase.php';

class FormatterDirectTest extends \Tests\HuaForms\HuaFormsTestCase
{
    
    /**
     * Test formatter sans type
     */
    public function testFormatterNoTypeException() : void
    {
        $formatter = new \HuaForms\Formatter();
        $this->expectException(\InvalidArgumentException::class);
        $formatter->format([], 'test');
    }
    
    /**
     * Test formatter type invalide
     */
    public function testFormatterInvalidTypeException() : void
    {
        $formatter = new \HuaForms\Formatter();
        $this->expectException(\InvalidArgumentException::class);
        $formatter->format(['type' => 'invalidtype'], 'test');
    }
    
    /**
     * Test direct du formatter "trim"
     * @dataProvider trimProvider
     */
    public function testFormatterTrim(string $value, string $expected) : void
    {
        $formatter = new \HuaForms\Formatter();
        $result = $formatter->format(['type' => 'trim'], $value);
        $this->assertSame($expected, $result);
    }
    public function trimProvider() : array
    {
        return [
            ['test 1', 'test 1'],
            [' Test 2', 'Test 2'],
            ['   Test 3'."\t", 'Test 3'],
            ['Test 4'."\n\n", 'Test 4']
        ];
    }
    
    /**
     * Test direct du formatter "trim" : exception si la valeur est un tableau
     */
    public function testFormatterTrimException() : void
    {
        $formatter = new \HuaForms\Formatter();
        $this->expectException(\InvalidArgumentException::class);
        $formatter->format(['type' => 'trim'], ['test']);
    }
    
    
    /**
     * Test direct du formatter "number"
     * @dataProvider numberProvider
     */
    public function testFormatterNumber(string $value, $expected, string $type) : void
    {
        $formatter = new \HuaForms\Formatter();
        $result = $formatter->format(['type' => 'number'], $value);
        $this->assertEquals($expected, $result);
        if ($type === 'int') {
            $this->assertIsInt($result);
        } else if ($type === 'float') {
            $this->assertIsFloat($result);
        } else if ($type === 'string') {
            $this->assertIsString($result);
        }
    }
    public function numberProvider() : array
    {
        return [
            ['123', 123, 'int'],
            ['', '', 'string'], // invalid
            ['0', 0, 'int'],
            ['1e6', '1e6', 'string'], // invalid
            ['0x6', '0x6', 'string'], // invalid
            ['01234', 1234, 'int'],
            ['-123', -123, 'int'],
            ['-0123', -123, 'int'],
            ['--123', '--123', 'string'], // invalid
            ['+123', '+123', 'string'], // invalid
            ['12.34', 12.34, 'float'],
            ['-12.345', -12.345, 'float'],
        ];
    }
    
    /**
     * Test direct du formatter "number" : exception si la valeur est un tableau
     */
    public function testFormatterNumberException() : void
    {
        $formatter = new \HuaForms\Formatter();
        $this->expectException(\InvalidArgumentException::class);
        $formatter->format(['type' => 'number'], ['123']);
    }
    
    /**
     * Test direct du formatter "checkbox"
     * @dataProvider checkboxProvider
     */
    public function testFormatterCheckbox(string $value, $expected, string $type) : void
    {
        $formatter = new \HuaForms\Formatter();
        $result = $formatter->format(['type' => 'checkbox'], $value);
        $this->assertEquals($expected, $result);
        if ($type === 'bool') {
            $this->assertIsBool($result);
        } else if ($type === 'string') {
            $this->assertIsString($result);
        }
    }
    public function checkboxProvider() : array
    {
        return [
            ['test', 'test', 'string'],
            ['on', true, 'bool'],
            ['', false, 'bool'],
        ];
    }
}