<?php namespace Timmachine\PhpJsonRpc;


class RouterTest extends \PHPUnit_Framework_TestCase
{

    protected static  $router;

    public static function setUpBeforeClass()
    {
        self::$router = new Router();
    }

    public function testCanAddRoute()
    {

        $this->assertNull(self::$router->add('test', 'Controller@method'));
    }

    public function testMethodExist()
    {
        $this->assertTrue(self::$router->exist('test'));
    }
}
 