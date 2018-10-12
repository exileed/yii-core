<?php
/**
 * @link http://www.yiiframework.com/
 * @copyright Copyright (c) 2008 Yii Software LLC
 * @license http://www.yiiframework.com/license/
 */

namespace yii\tests\framework\validators;

use yii\tests\TestCase;
use yii\validators\JsonValidator;

/**
 * @group validators
 */
class JsonValidatorTest extends TestCase
{
    protected function setUp()
    {
        parent::setUp();

        // destroy application, Validator must work without $this->app
        $this->destroyApplication();
    }

    public function testValidateValue()
    {
        $string = '{ "json": "string" }';
        $val = new JsonValidator();
        $this->assertTrue($val->validate($string));
        $this->assertFalse($val->validate('5'));
        $this->assertFalse($val->validate(null));
        $this->assertFalse($val->validate(' '));
        $this->assertFalse($val->validate(''));
        $this->assertFalse($val->validate(false));
        $this->assertFalse($val->validate([]));
    }

    public function testErrorMessage()
    {
        $validator = new JsonValidator;

        $validator->validate('someIncorrectValue', $errorMessage);

        $this->assertEquals('must be valid JSON string', $errorMessage);
    }
}
