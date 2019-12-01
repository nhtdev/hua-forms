<?php

namespace Tests\HuaForms;

require_once dirname(__FILE__).'/HuaFormsTestCase.php';

class ValidatorDirectTest extends \Tests\HuaForms\HuaFormsTestCase
{
    
    /**
     * Test validator sans type
     */
    public function testValidatorNoTypeException() : void
    {
        $validator = new \HuaForms\Validator();
        $this->expectException(\InvalidArgumentException::class);
        $validator->validate([], 'test');
    }
    
    /**
     * Test validator type invalide
     */
    public function testValidatorInvalidTypeException() : void
    {
        $validator = new \HuaForms\Validator();
        $this->expectException(\InvalidArgumentException::class);
        $validator->validate(['type' => 'invalidtype'], 'test');
    }
    
    /**
     * Test direct du validator "required"
     * @dataProvider requiredProvider
     */
    public function testValidatorRequired($value, bool $expected) : void
    {
        $validator = new \HuaForms\Validator();
        $result = $validator->validate(['type' => 'required'], $value);
        $this->assertEquals($expected, $result);
    }
    public function requiredProvider() : array
    {
        return [
            ['test', true],
            ['1', true],
            ['0', true],
            ['', false],
            [null, false],
            [false, false],
            [true, true],
        ];
    }
    
    /**
     * Test direct du validator "maxlength"
     * @dataProvider maxlengthProvider
     */
    public function testValidatorMaxlength(int $maxlength, string $value, bool $expected) : void
    {
        $validator = new \HuaForms\Validator();
        $result = $validator->validate(['type' => 'maxlength', 'maxlength' => $maxlength], $value);
        $this->assertEquals($expected, $result);
    }
    public function maxlengthProvider() : array
    {
        return [
            [5, 'test', true],
            [4, 'test', true],
            [3, 'test', false],
            [0, '', true],
            [0, 'x', false],
        ];
    }
    
    /**
     * Test direct du validator "maxlength" : exception si la valeur est un tableau
     */
    public function testValidatorMaxlengthException() : void
    {
        $validator = new \HuaForms\Validator();
        $this->expectException(\InvalidArgumentException::class);
        $validator->validate(['type' => 'maxlength', 'maxlength' => 5], ['test']);
    }
    
    /**
     * Test direct du validator "maxlength" : exception paramètre maxlength incomplet
     */
    public function testValidatorMaxlengthException2() : void
    {
        $validator = new \HuaForms\Validator();
        $this->expectException(\InvalidArgumentException::class);
        $validator->validate(['type' => 'maxlength'], 'test');
    }
    
    /**
     * Test direct du validator "minlength"
     * @dataProvider minlengthProvider
     */
    public function testValidatorMinlength(int $minlength, string $value, bool $expected) : void
    {
        $validator = new \HuaForms\Validator();
        $result = $validator->validate(['type' => 'minlength', 'minlength' => $minlength], $value);
        $this->assertEquals($expected, $result);
    }
    public function minlengthProvider() : array
    {
        return [
            [5, 'test', false],
            [4, 'test', true],
            [3, 'test', true],
            [0, '', true],
            [0, 'x', true],
        ];
    }
    
    /**
     * Test direct du validator "minlength" : exception si la valeur est un tableau
     */
    public function testValidatorMinlengthException() : void
    {
        $validator = new \HuaForms\Validator();
        $this->expectException(\InvalidArgumentException::class);
        $validator->validate(['type' => 'minlength', 'minlength' => 5], ['test']);
    }
    
    /**
     * Test direct du validator "minlength" : exception paramètre minlength incomplet
     */
    public function testValidatorMinlengthException2() : void
    {
        $validator = new \HuaForms\Validator();
        $this->expectException(\InvalidArgumentException::class);
        $validator->validate(['type' => 'minlength'], 'test');
    }
    
    /**
     * Test direct du validator "inarray"
     * @dataProvider inarrayProvider
     */
    public function testValidatorInarray(array $array, $value, bool $expected) : void
    {
        $validator = new \HuaForms\Validator();
        $result = $validator->validate(['type' => 'inarray', 'values' => $array], $value);
        $this->assertEquals($expected, $result);
    }
    public function inarrayProvider() : array
    {
        return [
            [['a', 'b'], 'a', true],
            [['a', 'b'], 'c', false],
            [['1', '2'], 1, true],
            [['1', '2'], 3, false],
            [[], '', false],
            [['a', 'b', 'c'], ['a', 'b'], true],
            [['a', 'b', 'c'], ['b', 'a'], true],
            [['a', 'b', 'c'], ['b', 'a', 'c'], true],
            [['a', 'b', 'c'], ['a', 'd'], false],
        ];
    }
    
    /**
     * Test direct du validator "inarray" : exception si "values" non défini
     */
    public function testValidatorInarrayException() : void
    {
        $validator = new \HuaForms\Validator();
        $this->expectException(\InvalidArgumentException::class);
        $validator->validate(['type' => 'inarray'], 'test');
    }
    
    /**
     * Test direct du validator "inarray" : exception si "values" n'est pas un tableau
     */
    public function testValidatorInarrayException2() : void
    {
        $validator = new \HuaForms\Validator();
        $this->expectException(\InvalidArgumentException::class);
        $validator->validate(['type' => 'inarray', 'values' => 'str'], 'test');
    }
    
    /**
     * Test direct du validator "email"
     * @dataProvider emailProvider
     */
    public function testValidatorEmail(string $value, bool $expected) : void
    {
        $validator = new \HuaForms\Validator();
        $result = $validator->validate(['type' => 'email'], $value);
        $this->assertEquals($expected, $result);
    }
    public function emailProvider() : array
    {
        return [
            ['test@gmail.com', true],
            ['testgmail.com', false],
            ['', false],
            ['t_est.test-123@gmail.fr', true],
            ['test.test+x@gmail.fr', true],
            ['test @gmail.fr', false],
        ];
    }
    
    /**
     * Test direct du validator "email" : exception si la valeur est un tableau
     */
    public function testValidatorEmailException() : void
    {
        $validator = new \HuaForms\Validator();
        $this->expectException(\InvalidArgumentException::class);
        $validator->validate(['type' => 'email'], ['test@gmail.com']);
    }
    
    /**
     * Test direct du validator "url"
     * @dataProvider urlProvider
     */
    public function testValidatorUrl(string $value, bool $expected) : void
    {
        $validator = new \HuaForms\Validator();
        $result = $validator->validate(['type' => 'url'], $value);
        $this->assertEquals($expected, $result);
    }
    public function urlProvider() : array
    {
        return [
            ['http://www.test.com', true],
            ['https://www.test.com/page.php', true],
            ['https://www.test.com/folder/page.php?v=1&v2=2&v3=t%20h%20x', true],
            ['www.google.fr', false],
            ['', false],
            ['ftp://www.test.com', true],
        ];
    }
    
    /**
     * Test direct du validator "url" : exception si la valeur est un tableau
     */
    public function testValidatorUrlException() : void
    {
        $validator = new \HuaForms\Validator();
        $this->expectException(\InvalidArgumentException::class);
        $validator->validate(['type' => 'url'], ['http://www.test.com']);
    }
    
    /**
     * Test direct du validator "color"
     * @dataProvider colorProvider
     */
    public function testValidatorColor(string $value, bool $expected) : void
    {
        $validator = new \HuaForms\Validator();
        $result = $validator->validate(['type' => 'color'], $value);
        $this->assertEquals($expected, $result);
    }
    public function colorProvider() : array
    {
        return [
            ['#123456', true],
            ['#7890ab', true],
            ['#cdef01', true],
            ['#cdef0100', false],
            ['#ffffff', true],
            ['#FFFFFF', false],
            ['ffffff', false],
            ['red', false],
            ['', false],
        ];
    }
    
    /**
     * Test direct du validator "color" : exception si la valeur est un tableau
     */
    public function testValidatorColorException() : void
    {
        $validator = new \HuaForms\Validator();
        $this->expectException(\InvalidArgumentException::class);
        $validator->validate(['type' => 'color'], ['#ff0000']);
    }
    
    /**
     * Test direct du validator "number"
     * @dataProvider numberProvider
     */
    public function testValidatorNumber($min, $max, $step, $value, $expected) : void
    {
        $validator = new \HuaForms\Validator();
        $result = $validator->validate(['type' => 'number', 'min' => $min, 'max' => $max, 'step' => $step], $value);
        $this->assertEquals($expected, $result);
    }
    public function numberProvider() : array
    {
        return [
            [null, null, null, 123, true],
            [null, null, null, '123', true],
            [null, null, null, '123.45', 'step'],
            [null, null, 0.01, '123.45', true],
            [null, null, 0.01, '123,45', false],
            [null, null, null, '-123.46', 'step'],
            [null, null, 0.01, '-123.45', true],
            [null, null, null, '.45', false],
            [null, null, null, '1e3', false],
            [null, null, null, '0123', true],
            [null, null, null, '+123', false],
            [null, null, null, '--123', false],
            [null, null, 0.0001, '0.0001', true],
            [null, null, 0.0001, '0', true],
            [null, null, 0.0001, '0.00011', 'step'],
            [null, null, 10, '80', true],
            [null, null, 10, '0', true],
            [null, null, 10, '100', true],
            [null, null, 10, '-100', true],
            [null, null, 7, '49', true],
            [0, 100, null, '49', true],
            [0, 100, null, '0', true],
            [0, 100, null, '100', true],
            [0, 100, 7, '49', true],
            [0, 100, null, '110', 'max'],
            [50, 100, null, '40', 'min'],
            [0, 100, null, '-49', 'min'],
            [0.0001, 0.0002, 0.00001, '0.00015', true],
            [-20, -10, null, '-15', true],
            [-20, -10, null, '-20', true],
            [-20, -10, null, '-10', true],
            [-20, -10, null, '-25', 'min'],
            [-20, -10, null, '-5', 'max'],
            [1, null, null, '1000', true],
            [1, null, null, '0', 'min'],
            [null, 100, null, '50', true],
            [null, 100, null, '-50', true],
            [null, 100, null, '1001', 'max'],
            [3, 100, 2, '3', true],
            [3, 100, 2, '5', true],
            [3, 100, 2, '6', 'step'],
            [3, 100, 2, '99', true],
            [3, 100, 2, '100', 'step'],
            [3, 100, 2, '101', 'max'],
        ];
    }
    
    /**
     * Test direct du validator "number" : exception si la valeur est un tableau
     */
    public function testValidatorNumberException() : void
    {
        $validator = new \HuaForms\Validator();
        $this->expectException(\InvalidArgumentException::class);
        $validator->validate(['type' => 'number'], ['123']);
    }
    
    /**
     * Test direct du validator "month"
     * @dataProvider monthProvider
     */
    public function testValidatorMonth($min, $max, string $value, $expected) : void
    {
        $validator = new \HuaForms\Validator();
        $result = $validator->validate(['type' => 'month', 'min' => $min, 'max' => $max], $value);
        $this->assertEquals($expected, $result);
    }
    public function monthProvider() : array
    {
        return [
            [null, null, '2019-01', true],
            [null, null, '2019-W01', false],
            [null, null, '201901', false],
            [null, null, 'test', false],
            [null, null, '2019-00', false],
            [null, null, '2019-1', false],
            [null, null, '2019-13', false],
            ['2019-01', '2019-01', '2019-01', true],
            ['2019-01', '2019-03', '2019-01', true],
            ['2019-01', '2019-03', '2019-02', true],
            ['2019-01', '2019-03', '2019-03', true],
            ['2019-01', '2019-03', '2019-04', 'max'],
            ['2019-02', '2019-03', '2019-01', 'min'],
            ['2019-01', '2019-03', '2018-12', 'min'],
            ['2019-01', '2019-03', '2018-03', 'min'],
            ['2010-03', '2019-03', '2009-03', 'min'],
            ['2010-03', '2019-03', '2010-01', 'min'],
            ['2010-03', '2019-03', '2020-01', 'max'],
            ['2010-03', '2019-03', '2019-04', 'max'],
        ];
    }
    
    /**
     * Test direct du validator "month" : exception si la valeur est un tableau
     */
    public function testValidatorMonthException() : void
    {
        $validator = new \HuaForms\Validator();
        $this->expectException(\InvalidArgumentException::class);
        $validator->validate(['type' => 'month'], ['2019-01']);
    }
    
    /**
     * Test direct du validator "week"
     * @dataProvider weekProvider
     */
    public function testValidatorWeek($min, $max, string $value, $expected) : void
    {
        $validator = new \HuaForms\Validator();
        $result = $validator->validate(['type' => 'week', 'min' => $min, 'max' => $max], $value);
        $this->assertEquals($expected, $result);
    }
    public function weekProvider() : array
    {
        return [
            [null, null, '2019-W01', true],
            [null, null, '2019-W1', false],
            [null, null, '2019-01', false],
            [null, null, 'test', false],
            [null, null, '2019-w01', false],
            [null, null, '2019-W52', true],
            [null, null, '2019-W53', false],
            [null, null, '2015-W53', true],
            ['2019-W01', '2019-W01', '2019-W01', true],
            ['2019-W01', '2019-W03', '2019-W01', true],
            ['2019-W01', '2019-W03', '2019-W02', true],
            ['2019-W01', '2019-W03', '2019-W03', true],
            ['2019-W01', '2019-W03', '2019-W04', 'max'],
            ['2019-W02', '2019-W03', '2019-W01', 'min'],
            ['2019-W01', '2019-W03', '2018-W12', 'min'],
            ['2019-W01', '2019-W03', '2018-W03', 'min'],
            ['2010-W03', '2019-W03', '2009-W03', 'min'],
            ['2010-W03', '2019-W03', '2010-W01', 'min'],
            ['2010-W03', '2019-W03', '2020-W01', 'max'],
            ['2010-W03', '2019-W03', '2019-W04', 'max'],
        ];
    }
    
    /**
     * Test direct du validator "week" : exception si la valeur est un tableau
     */
    public function testValidatorWeekException() : void
    {
        $validator = new \HuaForms\Validator();
        $this->expectException(\InvalidArgumentException::class);
        $validator->validate(['type' => 'week'], ['2019-W01']);
    }
    
    /**
     * Test direct du validator "date"
     * @dataProvider dateProvider
     */
    public function testValidatorDate($min, $max, string $value, $expected) : void
    {
        $validator = new \HuaForms\Validator();
        $result = $validator->validate(['type' => 'date', 'min' => $min, 'max' => $max], $value);
        $this->assertEquals($expected, $result);
    }
    public function dateProvider() : array
    {
        return [
            [null, null, '2019-01-25', true],
            [null, null, '2019-01', false],
            [null, null, '2019-01-8', false],
            [null, null, '2019-1-18', false],
            [null, null, 'test', false],
            [null, null, '2019-01-32', false],
            [null, null, '2019-02-29', false],
            [null, null, '2020-02-29', true],
            [null, null, '2044-02-29', true],
            ['2018-02-10', '2019-06-20', '2017-03-15', 'min'],
            ['2018-02-10', '2019-06-20', '2020-03-15', 'max'],
            ['2018-02-10', '2019-06-20', '2018-01-15', 'min'],
            ['2018-02-10', '2019-06-20', '2019-07-15', 'max'],
            ['2018-02-10', '2019-06-20', '2018-02-09', 'min'],
            ['2018-02-10', '2019-06-20', '2019-06-21', 'max'],
            ['2018-02-10', '2019-06-20', '2018-02-10', true],
            ['2018-02-10', '2019-06-20', '2019-06-20', true],
        ];
    }
    
    /**
     * Test direct du validator "date" : exception si la valeur est un tableau
     */
    public function testValidatorDateException() : void
    {
        $validator = new \HuaForms\Validator();
        $this->expectException(\InvalidArgumentException::class);
        $validator->validate(['type' => 'date'], ['2019-01-01']);
    }
    
    /**
     * Test direct du validator "time"
     * @dataProvider timeProvider
     */
    public function testValidatorTime($min, $max, $step, string $value, $expected) : void
    {
        $validator = new \HuaForms\Validator();
        $result = $validator->validate(['type' => 'time', 'min' => $min, 'max' => $max, 'step' => $step], $value);
        $this->assertEquals($expected, $result);
    }
    public function timeProvider() : array
    {
        return [
            [null, null, null, '00:00', true],
            [null, null, null, '12:34', true],
            [null, null, null, '08:34', true],
            [null, null, null, '8:34', false],
            [null, null, null, '18:04', true],
            [null, null, null, '18:4', false],
            [null, null, null, 'test', false],
            [null, null, null, '', false],
            [null, null, null, '25:04', false],
            [null, null, null, '24:00', false],
            [null, null, null, '00:00', true],
            [null, null, null, '23:59', true],
            [null, null, null, '12:60', false],
            [null, null, null, '12:99', false],
            [null, null, 1, '23:59:59', true],
            [null, null, 1, '12:12:60', false],
            [null, null, 1, '12:12:99', false],
            [null, null, null, '12:34:56', false],
            [null, null, 1, '12:34:56', true],
            [null, null, 30, '12:34:56', 'step'],
            [null, null, 30, '12:34:30', true],
            [null, null, 30, '12:34:00', true],
            [null, null, 30, '12:34:0', false],
            [null, null, 30, '12:34', false],
            ['08:20', '11:35', null, '10:00', true],
            ['08:20', '11:35', null, '07:30', 'min'],
            ['08:20', '11:35', null, '12:30', 'max'],
            ['08:20', '11:35', null, '08:15', 'min'],
            ['08:20', '11:35', null, '11:40', 'max'],
            ['08:20', '11:35', null, '08:19', 'min'],
            ['08:20', '11:35', null, '11:36', 'max'],
            ['08:20', '11:35', null, '08:20', true],
            ['08:20', '11:35', null, '11:35', true],
            ['08:21', '11:35', 300, '08:25', 'step'],
            ['08:21', '11:35', 300, '08:26', true],
            ['08:21', '11:35', 300, '08:27', 'step'],
            ['08:21', '11:35', 300, '11:31', true],
            ['08:21', '11:35', 300, '11:35', 'step'],
            ['08:21', '11:35', 300, '11:36', 'max'],
            ['00:00', '11:35', 900, '11:35', 'step'], // step 15 min
            ['00:00', '11:35', 900, '11:15', true], // step 15 min
            ['00:00', '11:35', 900, '11:45', 'max'], // step 15 min
            ['00:00', '11:35', 3600, '11:35', 'step'], // step 1h
            ['00:00', '11:35', 3600, '11:00', true], // step 1h
            ['00:00', '11:35', 3600, '12:00', 'max'], // step 1h
            ['11:35', '08:20', null, '10:00', 'inverse'],
            ['11:35', '08:20', null, '11:34', 'inverse'],
            ['11:35', '08:20', null, '11:35', true],
            ['11:35', '08:20', null, '11:36', true],
            ['11:35', '08:20', null, '08:21', 'inverse'],
            ['11:35', '08:20', null, '08:20', true],
            ['11:35', '08:20', null, '08:19', true],
            ['08:20:10', '11:35:40', 1, '10:45:22', true],
            ['08:20:10', '11:35:40', 1, '08:20:10', true],
            ['08:20:10', '11:35:40', 1, '08:20:09', 'min'],
            ['08:20:10', '11:35:40', 1, '11:35:40', true],
            ['08:20:10', '11:35:40', 1, '11:35:41', 'max'],
            ['08:20:10', '11:35:40', 30, '08:20:40', true],
            ['08:20:10', '11:35:40', 30, '08:20:30', 'step'],
            ['08:20:10', '11:35:40', 30, '11:35:39', 'step'],
            ['08:20:10', '11:35:40', 30, '11:35:40', true],
            ['08:20:10', '11:35:40', 30, '11:36:10', 'max'],
            ['11:35:10', '08:20:45', 1, '10:00:00', 'inverse'],
            ['11:35:10', '08:20:45', 1, '11:35:09', 'inverse'],
            ['11:35:10', '08:20:45', 1, '11:35:10', true],
            ['11:35:10', '08:20:45', 1, '11:35:11', true],
            ['11:35:10', '08:20:45', 1, '08:20:46', 'inverse'],
            ['11:35:10', '08:20:45', 1, '08:20:45', true],
            ['11:35:10', '08:20:45', 1, '08:20:44', true],
        ];
    }
    
    /**
     * Test direct du validator "time" : exception si la valeur est un tableau
     */
    public function testValidatorTimeException() : void
    {
        $validator = new \HuaForms\Validator();
        $this->expectException(\InvalidArgumentException::class);
        $validator->validate(['type' => 'time'], ['01:23']);
    }
    
    /**
     * Test direct du validator "datetime-local"
     * @dataProvider datetimelocalProvider
     */
    public function testValidatorDatetimelocal($min, $max, $step, string $value, $expected) : void
    {
        $validator = new \HuaForms\Validator();
        $result = $validator->validate(['type' => 'datetime-local', 'min' => $min, 'max' => $max, 'step' => $step], $value);
        $this->assertEquals($expected, $result);
    }
    public function datetimelocalProvider() : array
    {
        return [
            [null, null, null, '2019-01-25T12:12', true],
            [null, null, null, '2019-01T12:12', false],
            [null, null, null, '2019-01-8T12:12', false],
            [null, null, null, '2019-1-18T12:12', false],
            [null, null, null, 'test', false],
            [null, null, null, '', false],
            [null, null, null, '2019-01-32T12:12', false],
            [null, null, null, '2019-02-29T12:12', false],
            [null, null, null, '2020-02-29T12:12', true],
            [null, null, null, '2044-02-29T12:12', true],
            [null, null, null, '2019-01-25T00:00', true],
            [null, null, null, '2019-01-25T12:34', true],
            [null, null, null, '2019-01-25T08:34', true],
            [null, null, null, '2019-01-25T8:34', false],
            [null, null, null, '2019-01-25T18:04', true],
            [null, null, null, '2019-01-25T18:4', false],
            [null, null, null, '2019-01-25T25:04', false],
            [null, null, null, '2019-01-25T24:00', false],
            [null, null, null, '2019-01-25T00:00', true],
            [null, null, null, '2019-01-25T23:59', true],
            [null, null, null, '2019-01-25T12:60', false],
            [null, null, null, '2019-01-25T12:99', false],
            ['2018-02-10T08:25', '2019-06-20T08:25', null, '2017-03-15T08:25', 'min'],
            ['2018-02-10T08:25', '2019-06-20T08:25', null, '2020-03-15T08:25', 'max'],
            ['2018-02-10T08:25', '2019-06-20T08:25', null, '2018-01-15T08:25', 'min'],
            ['2018-02-10T08:25', '2019-06-20T08:25', null, '2019-07-15T08:25', 'max'],
            ['2018-02-10T08:25', '2019-06-20T08:25', null, '2018-02-09T08:25', 'min'],
            ['2018-02-10T08:25', '2019-06-20T08:25', null, '2019-06-21T08:25', 'max'],
            ['2018-02-10T08:25', '2019-06-20T08:25', null, '2018-02-10T08:25', true],
            ['2018-02-10T08:25', '2019-06-20T08:25', null, '2019-06-20T08:25', true],
            [null, null, 1, '2019-06-20T23:59:59', true],
            [null, null, 1, '2019-06-20T12:12:60', false],
            [null, null, 1, '2019-06-20T12:12:99', false],
            [null, null, null, '2019-06-20T12:34:56', false],
            [null, null, 1, '2019-06-20T12:34:56', true],
            [null, null, 30, '2019-06-20T12:34:56', 'step'],
            [null, null, 30, '2019-06-20T12:34:30', true],
            [null, null, 30, '2019-06-20T12:34:00', true],
            [null, null, 30, '2019-06-20T12:34:0', false],
            [null, null, 30, '2019-06-20T12:34', false],
            ['2019-06-20T08:20', '2019-06-20T11:35', null, '2019-06-20T10:00', true],
            ['2019-06-20T08:20', '2019-06-20T11:35', null, '2019-06-20T07:30', 'min'],
            ['2019-06-20T08:20', '2019-06-20T11:35', null, '2019-06-20T12:30', 'max'],
            ['2019-06-20T08:20', '2019-06-20T11:35', null, '2019-06-20T08:15', 'min'],
            ['2019-06-20T08:20', '2019-06-20T11:35', null, '2019-06-20T11:40', 'max'],
            ['2019-06-20T08:20', '2019-06-20T11:35', null, '2019-06-20T08:19', 'min'],
            ['2019-06-20T08:20', '2019-06-20T11:35', null, '2019-06-20T11:36', 'max'],
            ['2019-06-20T08:20', '2019-06-20T11:35', null, '2019-06-20T08:20', true],
            ['2019-06-20T08:20', '2019-06-20T11:35', null, '2019-06-20T11:35', true],
            ['2019-06-20T08:21', '2019-06-20T11:35', 300, '2019-06-20T08:25', 'step'],
            ['2019-06-20T08:21', '2019-06-20T11:35', 300, '2019-06-20T08:26', true],
            ['2019-06-20T08:21', '2019-06-20T11:35', 300, '2019-06-20T08:27', 'step'],
            ['2019-06-20T08:21', '2019-06-20T11:35', 300, '2019-06-20T11:31', true],
            ['2019-06-20T08:21', '2019-06-20T11:35', 300, '2019-06-20T11:35', 'step'],
            ['2019-06-20T08:21', '2019-06-20T11:35', 300, '2019-06-20T11:36', 'max'],
            ['2019-06-20T00:00', '2019-06-20T11:35', 900, '2019-06-20T11:35', 'step'], // step 15 min
            ['2019-06-20T00:00', '2019-06-20T11:35', 900, '2019-06-20T11:15', true], // step 15 min
            ['2019-06-20T00:00', '2019-06-20T11:35', 900, '2019-06-20T11:45', 'max'], // step 15 min
            ['2019-06-20T00:00', '2019-06-20T11:35', 3600, '2019-06-20T11:35', 'step'], // step 1h
            ['2019-06-20T00:00', '2019-06-20T11:35', 3600, '2019-06-20T11:00', true], // step 1h
            ['2019-06-20T00:00', '2019-06-20T11:35', 3600, '2019-06-20T12:00', 'max'], // step 1h
            ['2019-06-20T08:20:10', '2019-06-20T11:35:40', 1, '2019-06-20T10:45:22', true],
            ['2019-06-20T08:20:10', '2019-06-20T11:35:40', 1, '2019-06-20T08:20:10', true],
            ['2019-06-20T08:20:10', '2019-06-20T11:35:40', 1, '2019-06-20T08:20:09', 'min'],
            ['2019-06-20T08:20:10', '2019-06-20T11:35:40', 1, '2019-06-20T11:35:40', true],
            ['2019-06-20T08:20:10', '2019-06-20T11:35:40', 1, '2019-06-20T11:35:41', 'max'],
            ['2019-06-20T08:20:10', '2019-06-20T11:35:40', 30, '2019-06-20T08:20:40', true],
            ['2019-06-20T08:20:10', '2019-06-20T11:35:40', 30, '2019-06-20T08:20:30', 'step'],
            ['2019-06-20T08:20:10', '2019-06-20T11:35:40', 30, '2019-06-20T11:35:39', 'step'],
            ['2019-06-20T08:20:10', '2019-06-20T11:35:40', 30, '2019-06-20T11:35:40', true],
            ['2019-06-20T08:20:10', '2019-06-20T11:35:40', 30, '2019-06-20T11:36:10', 'max'],
        ];
    }
    
    /**
     * Test direct du validator "datetime-local" : exception si la valeur est un tableau
     */
    public function testValidatorDatetimelocalException() : void
    {
        $validator = new \HuaForms\Validator();
        $this->expectException(\InvalidArgumentException::class);
        $validator->validate(['type' => 'datetime-local'], ['2019-01-01T01:23']);
    }
    
    /**
     * Test direct du validator "upload-error"
     * @dataProvider uploaderrorProvider
     */
    public function testValidatorUploadError($files, $expected) : void
    {
        $validator = new \HuaForms\Validator();
        $result = $validator->validate(['type' => 'upload-error'], $files);
        $this->assertEquals($expected, $result);
    }
    public function uploaderrorProvider() : array
    {
        $tmpName = '/tmp/upload_'.uniqid();
        file_put_contents($tmpName, 'xxx');
        $result = [
            [new \HuaForms\File(['error' => UPLOAD_ERR_OK, 'name' => 'Fichier.pdf', 'tmp_name' => $tmpName, 'size' => 3, 'type' => 'application/pdf'], false), true],
            [new \HuaForms\File(['error' => UPLOAD_ERR_OK, 'name' => 'Fichier.pdf', 'tmp_name' => $tmpName, 'size' => 3, 'type' => 'application/pdf'], true), false],
            [new \HuaForms\File(['error' => UPLOAD_ERR_OK, 'name' => 'Fichier.pdf', 'tmp_name' => $tmpName.'x', 'size' => 3, 'type' => 'application/pdf'], false), false],
            [new \HuaForms\File(['error' => UPLOAD_ERR_CANT_WRITE, 'name' => 'Fichier.pdf', 'tmp_name' => $tmpName, 'size' => 3, 'type' => 'application/pdf'], false), false],
            [new \HuaForms\File(['error' => UPLOAD_ERR_EXTENSION, 'name' => 'Fichier.pdf', 'tmp_name' => $tmpName, 'size' => 3, 'type' => 'application/pdf'], false), false],
            [new \HuaForms\File(['error' => UPLOAD_ERR_FORM_SIZE, 'name' => 'Fichier.pdf', 'tmp_name' => $tmpName, 'size' => 3, 'type' => 'application/pdf'], false), false],
            [new \HuaForms\File(['error' => UPLOAD_ERR_INI_SIZE, 'name' => 'Fichier.pdf', 'tmp_name' => $tmpName, 'size' => 3, 'type' => 'application/pdf'], false), false],
            [new \HuaForms\File(['error' => UPLOAD_ERR_NO_FILE, 'name' => 'Fichier.pdf', 'tmp_name' => $tmpName, 'size' => 3, 'type' => 'application/pdf'], false), false],
            [new \HuaForms\File(['error' => UPLOAD_ERR_NO_TMP_DIR, 'name' => 'Fichier.pdf', 'tmp_name' => $tmpName, 'size' => 3, 'type' => 'application/pdf'], false), false],
            [new \HuaForms\File(['error' => UPLOAD_ERR_PARTIAL, 'name' => 'Fichier.pdf', 'tmp_name' => $tmpName, 'size' => 3, 'type' => 'application/pdf'], false), false],
        ];
        // Idem en upload multiple avec un fichier valide + celui valide ou non
        $result2 = $result;
        foreach ($result as $row) {
            $newRow = [
                [$row[0], new \HuaForms\File(['error' => UPLOAD_ERR_OK, 'name' => 'Fichier.pdf', 'tmp_name' => $tmpName, 'size' => 3, 'type' => 'application/pdf'], false)],
                $row[1]
            ];
            $result2[] = $newRow;
        }
        return $result2;
    }
    
    /**
     * Test direct du validator "upload-error" : exception si la valeur n'est pas un fichier
     */
    public function testValidatorUploadErrorException() : void
    {
        $validator = new \HuaForms\Validator();
        $this->expectException(\InvalidArgumentException::class);
        $validator->validate(['type' => 'upload-error'], 'test');
    }
    
    /**
     * Test direct du validator "upload-error" : exception si la valeur est un tableau et qu'une entrée n'est pas un fichier
     */
    public function testValidatorUploadErrorMultipleException() : void
    {
        $validator = new \HuaForms\Validator();
        $this->expectException(\InvalidArgumentException::class);
        $validator->validate(['type' => 'upload-error'], 
            [
                new \HuaForms\File(['error' => UPLOAD_ERR_NO_FILE, 'name' => '', 'tmp_name' => '', 'size' => 0, 'type' => ''], false),
                'test'
            ]);
    }
    /**
     * Test direct du validator "accept"
     * @dataProvider acceptProvider
     */
    public function testValidatorAccept(array $formats, $files, $expected) : void
    {
        $validator = new \HuaForms\Validator();
        $result = $validator->validate(['type' => 'accept', 'formats' => $formats], $files);
        $this->assertEquals($expected, $result);
    }
    public function acceptProvider() : array
    {
        $tmpName = '/tmp/upload_pdf';
        file_put_contents($tmpName, 'xxx'); // text/plain
        $pdfFile = new \HuaForms\File(['error' => UPLOAD_ERR_OK, 'name' => 'Fichier.pdf', 'tmp_name' => $tmpName, 'size' => 3, 'type' => 'application/pdf'], false);
        
        $tmpName = '/tmp/upload_png';
        file_put_contents($tmpName, base64_decode('iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAYAAAAfFcSJAAAADUlEQVR42mNk+P+/HgAFhAJ/wlseKgAAAABJRU5ErkJggg==')); // image/png
        $pngFile = new \HuaForms\File(['error' => UPLOAD_ERR_OK, 'name' => 'Fichier.png', 'tmp_name' => $tmpName, 'size' => 3, 'type' => 'image/png'], false);
        
        $pngFileError = clone $pngFile;
        $pngFileError->error = UPLOAD_ERR_CANT_WRITE;
        
        $pngFileUppercase = clone $pngFile;
        $pngFileUppercase->name = 'FICHIER.PNG';
        
        return [
            [['.pdf'], $pdfFile, true],
            [['.docx'], $pdfFile, false],
            [['text/plain'], $pdfFile, true],
            [['text/autre'], $pdfFile, false],
            [['text/*'], $pdfFile, false],
            [['.png'], $pngFile, true],
            [['.png'], $pngFileUppercase, true],
            [['.PNG'], $pngFile, true],
            [['.PNG'], $pngFileUppercase, true],
            [['.jpg'], $pngFile, false],
            [['image/png'], $pngFile, true],
            [['image/gif'], $pngFile, false],
            [['image/*'], $pngFile, true],
            [['.pdf'], [$pdfFile, $pdfFile, $pdfFile], true],
            [['.pdf'], [$pdfFile, $pdfFile, $pngFile], false],
            [['.pdf', '.png'], [$pdfFile, $pdfFile, $pngFile, $pngFile], true],
            [['.pdf', '.png'], [$pdfFile, $pdfFile, $pngFile, $pngFileUppercase], true],
            [['.PDF', '.PNG'], [$pdfFile, $pdfFile, $pngFile, $pngFile], true],
            [['.PDF', '.PNG'], [$pdfFile, $pdfFile, $pngFile, $pngFileUppercase], true],
            [['.pdf', 'image/png'], [$pdfFile, $pdfFile, $pngFile], true],
            [['.pdf', 'image/*'], [$pdfFile, $pdfFile, $pngFile], true],
            [['text/plain', 'image/*'], [$pdfFile, $pdfFile, $pngFile], true],
            [['text/plain', 'image/png'], [$pdfFile, $pdfFile, $pngFile], true],
            [['text/plain', 'image/other'], [$pdfFile, $pdfFile, $pngFile], false],
            [['.pdf', '.png'], [$pdfFile, $pdfFile, $pngFile, $pngFileError], true], // File with error ignored
            [[], $pngFileError, true], // File with error ignored
            [[], [$pngFileError, $pdfFile], false], // File with error ignored
            
        ];
    }
    
    /**
     * Test direct du validator "accept" : exception si la valeur n'est pas un fichier
     */
    public function testValidatorAcceptException() : void
    {
        $validator = new \HuaForms\Validator();
        $this->expectException(\InvalidArgumentException::class);
        $validator->validate(['type' => 'accept', 'formats' => ['.pdf']], 'test');
    }
    
    /**
     * Test direct du validator "accept" : exception si la valeur est un tableau et qu'une entrée n'est pas un fichier
     */
    public function testValidatorAcceptMultipleException() : void
    {
        $validator = new \HuaForms\Validator();
        $this->expectException(\InvalidArgumentException::class);
        $validator->validate(['type' => 'accept', 'formats' => ['.pdf']],
            [
                new \HuaForms\File(['error' => UPLOAD_ERR_NO_FILE, 'name' => '', 'tmp_name' => '', 'size' => 0, 'type' => ''], false),
                'test'
            ]);
    }
    
    /**
     * Test direct du validator "accept" : exception si pas de paramètre "formats"
     */
    public function testValidatorAcceptException2() : void
    {
        $validator = new \HuaForms\Validator();
        $this->expectException(\InvalidArgumentException::class);
        $validator->validate(['type' => 'accept'], new \HuaForms\File(['error' => UPLOAD_ERR_NO_FILE, 'name' => '', 'tmp_name' => '', 'size' => 0, 'type' => ''], false));
    }
    
    /**
     * Test direct du validator "accept" : exception si le paramètre "formats" n'est pas un tableau
     */
    public function testValidatorAcceptException3() : void
    {
        $validator = new \HuaForms\Validator();
        $this->expectException(\InvalidArgumentException::class);
        $validator->validate(['type' => 'accept', 'formats' => '.pdf'], new \HuaForms\File(['error' => UPLOAD_ERR_NO_FILE, 'name' => '', 'tmp_name' => '', 'size' => 0, 'type' => ''], false));
    }
    
    
    /**
     * Test direct du validator "pattern"
     * @dataProvider patternProvider
     */
    public function testValidatorPattern(string $pattern, string $value, bool $expected) : void
    {
        $validator = new \HuaForms\Validator();
        $result = $validator->validate(['type' => 'pattern', 'pattern' => $pattern], $value);
        $this->assertEquals($expected, $result);
    }
    public function patternProvider() : array
    {
        return [
            ['.*', '123 *ùdsf / \\^$', true],
            ['\d\d\d', '123', true],
            ['\d\d\d', '12', false],
            ['\d\d\d', '1234', false],
            ['\d\d\d', 'abc', false],
        ];
    }
    
    /**
     * Test direct du validator "pattern" : exception si la valeur est un tableau
     */
    public function testValidatorPatternException() : void
    {
        $validator = new \HuaForms\Validator();
        $this->expectException(\InvalidArgumentException::class);
        $validator->validate(['type' => 'pattern', 'pattern' => '.*'], ['test']);
    }
    
    /**
     * Test direct du validator "pattern" : exception paramètre pattern incomplet
     */
    public function testValidatorPatternException2() : void
    {
        $validator = new \HuaForms\Validator();
        $this->expectException(\InvalidArgumentException::class);
        $validator->validate(['type' => 'pattern'], 'test');
    }
    
}