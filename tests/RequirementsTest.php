<?php
/**
 * Created by PhpStorm.
 * User: tsmith
 * Date: 11/25/14
 * Time: 10:07 PM
 */

namespace Timmachine\PhpJsonRpc;


class RequirementsTest extends \PHPUnit_Framework_TestCase
{


    protected static $requirements;

    public static function setUpBeforeClass()
    {
        self::$requirements = new Requirements();
    }

    public function testCanAddRequirements()
    {
        self::$requirements->add('key', 'test', 'errorMessage', 'errorCode');
        $this->assertCount(1, self::$requirements->getRequirements());
    }

    public function testValidate()
    {
        $requirement =  new \stdClass();
        $requirement->key = 'test';
        self::$requirements->validate($requirement);
        $this->assertFalse(self::$requirements->isError());
    }
}
 