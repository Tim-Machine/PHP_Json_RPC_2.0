<?php
/**
 * Created by PhpStorm.
 * User: tsmith
 * Date: 11/25/14
 * Time: 10:58 PM
 */

namespace Timmachine\PhpJsonRpc;


class ListenerTest extends \PHPUnit_Framework_TestCase {

    public $listener;

    public function setUp()
    {

        $this->getMockBuilder('nonexistant')->setMockClassName('foo')
            ->setMethods(['bar'])
            ->getMock();


        $route = new Router();
        $route->add('test','\Timmachine\PhpJsonRpc\Requirements@setError');

        $this->listener = new Listener($route);

    }


    public function testValidateJson(){
        $json = '{"jsonrpc": "2.0", "method": "test", "params":{"id":5}, "id": 1}';
        $this->listener->validateJson($json);
        $this->assertTrue($this->listener->isValidJson());
    }

//    public function testProcessRequest()
//    {
//
//        print_r(class_exists('Timmachine\PhpJsonRpc\Listener'));
//
//        $result = $this->listener->processRequest();
//
//
//        print_r($result);
//    }

}
 