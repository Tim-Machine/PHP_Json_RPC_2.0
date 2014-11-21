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
    private $validMethod = true;

    /**
     * @var null
     */
    private $callable = true;

    /**
     * @var null
     */
    private $constructorArguments = null;

    private $methodArguments = null;

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
     * @return null
     */
    public function isCallable()
    {
        return $this->callable;
    }

    /**
     * @param null $callable
     */
    public function setCallable($callable)
    {
        $this->callable = $callable;
    }

    /**
     * @return null
     */
    public function getConstructorArguments()
    {
        return $this->constructorArguments;
    }

    /**
     * @param null $constructorArguments
     */
    public function setConstructorArguments($constructorArguments)
    {
        $this->constructorArguments = $constructorArguments;
    }

    /**
     * @return null
     */
    public function getMethodArguments()
    {
        return $this->methodArguments;
    }

    /**
     * @param null $methodArguments
     */
    public function setMethodArguments($methodArguments)
    {
        $this->methodArguments = $methodArguments;
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


    /**
     * @return $this
     * @throws RpcExceptions
     */
    private function checkMethodExist()
    {

        $class = $this->getClass();

        if (class_exists($class)) {
            $this->getClassConstructor($class);
            $class = new $class;
            $this->setCallable(method_exists($class, $this->getFunction()));

        } else {
            $this->setCallable(false);
            throw new RpcExceptions("Class [{$class}] does not exist or is unreachable" , -32601);
        }

        return $this;
    }

    /**
     * @param $class
     *
     * @return bool|\ReflectionParameter[]
     */
    private function getClassConstructor($class)
    {
        $refl = new \ReflectionClass($class);

        if ($con = $refl->getConstructor()) {
            $this->setConstructorArguments($con->getParameters());

            return true;
        } else {
            return false;
        }
    }


    public function getMethodArgs()
    {
        if ($this->checkMethodExist()->isCallable()) {
            $relf = new \ReflectionMethod($this->getClass(), $this->getFunction());
            $this->setMethodArguments($relf->getParameters());

            return $this->getMethodArguments();
        }

    }

    public function executeMethod(array $params)
    {

        if (count($this->getConstructorArguments()) > 0) {
            throw new RpcExceptions('Can not call a class that requires parameters for the constructor');
        }

        try {
            $className = $this->getClass();
            $method = new \ReflectionMethod($className,$this->getFunction());
            $results = $method->invokeArgs(new $className,$params);

            return $results;
        } catch (\Exception $e) {
            throw new RpcExceptions($e->getMessage(),-32600);
        }
    }

} 