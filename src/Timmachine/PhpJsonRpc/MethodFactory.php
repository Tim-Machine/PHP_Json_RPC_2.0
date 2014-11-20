<?php
namespace Timmachine\PhpJsonRpc;


/**
 * Class MethodFactory
 * @package Timmachine\PhpJsonRpc
 */
class MethodFactory
{

    /**
     * @var
     */
    private $method;
    /**
     * @var
     */
    private $class;
    /**
     * @var
     */
    private $function;
    /**
     * @var bool
     */
    public $validMethod = true;

    /**
     * @param $method
     *
     * @throws RpcExceptions
     */
    function __construct($method)
    {
        $this->setMethod($method);
        $this->setClassAndFunction();
    }

    /**
     * @return mixed
     */
    public function getClass()
    {
        return $this->class;
    }

    /**
     * @param mixed $class
     */
    public function setClass($class)
    {
        $this->class = $class;
    }

    /**
     * @return mixed
     */
    public function getFunction()
    {
        return $this->function;
    }

    /**
     * @param mixed $function
     */
    public function setFunction($function)
    {
        $this->function = $function;
    }

    /**
     * @return mixed
     */
    public function getMethod()
    {
        return $this->method;
    }

    /**
     * @param mixed $method
     */
    public function setMethod($method)
    {
        $this->method = $method;
    }

    /**
     * @return boolean
     */
    public function isValidMethod()
    {
        return $this->validMethod;
    }

    /**
     * @param boolean $validMethod
     */
    public function setValidMethod($validMethod)
    {
        $this->validMethod = $validMethod;
    }


    /**
     * @throws RpcExceptions
     */
    private function setClassAndFunction()
    {
        $methodParts = explode('@', $this->getMethod());
        if (count($methodParts) !== 2) {
            $this->setValidMethod(false);
            throw new RpcExceptions('incorrect method formatting');
        } else {
            $this->setClass($methodParts[0]);
            $this->setFunction($methodParts[1]);
        }
    }


} 