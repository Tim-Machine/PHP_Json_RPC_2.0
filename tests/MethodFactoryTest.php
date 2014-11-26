<?php
/**
 * Created by PhpStorm.
 * User: tsmith
 * Date: 11/25/14
 * Time: 10:38 PM
 */

namespace Timmachine\PhpJsonRpc;


class MethodFactoryTest extends \PHPUnit_Framework_TestCase
{
    public $methodFactory;

    public function setUp()
    {
        $this->getMockBuilder('nonexstant')->setMockClassName('foo')
            ->setMethods(['bar'])
            ->getMock();

        $this->methodFactory = new MethodFactory('foo@bar');
    }

    public function testSetClassAndFunction()
    {
        $this->assertEquals('foo', $this->methodFactory->getClass());

    }

    public function testSetFunction()
    {
        $this->assertEquals('bar', $this->methodFactory->getFunction());
    }

    public function testGetMethodArgs()
    {
        $this->assertCount(0, $this->methodFactory->getMethodArgs());
    }

    public function testExecuteMethod()
    {
        $this->assertNull($this->methodFactory->executeMethod([]));
    }
}
 