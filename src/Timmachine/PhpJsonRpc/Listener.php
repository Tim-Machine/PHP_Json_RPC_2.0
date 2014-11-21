<?php

namespace Timmachine\PhpJsonRpc;

use Timmachine\PhpJsonRpc\Requirements;
use Timmachine\PhpJsonRpc\Router;

class Listener
{

    private $ValidJson = true;

    private $method = '';

    private $params = [];

    private $id = 0;

    private $result;

    private $error = false;

    private $errorMessage = '';

    private $router;

    private $methodFactory;

    function __construct(Router $router)
    {
        $this->router = $router;
    }

    /**
     * @return boolean
     */
    public function isError()
    {
        return $this->error;
    }

    /**
     * @param boolean $error
     */
    public function setError($error)
    {
        $this->error = $error;
    }


    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param int $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * @return boolean
     */
    public function isValidJson()
    {
        return $this->ValidJson;
    }

    /**
     * @param boolean $isValidJson
     */
    public function setValidJson($isValidJson)
    {
        $this->ValidJson = $isValidJson;
    }

    /**
     * @return string
     */
    public function getMethod()
    {
        return $this->method;
    }

    /**
     * @param string $method
     */
    public function setMethod($method)
    {
        $this->method = $method;
    }

    /**
     * @return array
     */
    public function getParams()
    {
        return $this->params;
    }

    /**
     * @param array $params
     */
    public function setParams($params)
    {
        $this->params = $params;
    }

    /**
     * @return array
     */
    public function getResult()
    {
        return $this->result;
    }

    /**
     * @param mixed $result
     */
    public function setResult($result)
    {
        $this->result = $result;
    }

    /**
     * @return string
     */
    public function getErrorMessage()
    {
        return $this->errorMessage;
    }

    /**
     * @param string $errorMessage
     */
    public function setErrorMessage($message, $code)
    {
        $this->setError(true);
        $this->errorMessage = ['messge' => $message, 'code' => $code];
    }

    public function validateJson($json)
    {
        $request = json_decode($json);

        $requirements = new Requirements();

        $requirements->add('jsonrpc', null, 'PARSE ERROR :: missing protocol', -32700);
        $requirements->add('jsonrpc', "2.0", 'PARSE ERROR :: wrong version', -32700);
        $requirements->add('method', null, 'INVALID REQUEST :: missing method', -32600);
        $requirements->add('params', null, 'INVALID REQUEST :: missing params', -32600);

        $requirements->validate($request);
        if ($requirements->isError()) {
            $this->setValidJson(false);
            $this->setErrorMessage($requirements->getErrorMessage(), $requirements->getErrorCode());
        }

        if ($this->isValidJson()) {
            $this->setMethod($request->method);
            $this->setParams($request->params);
            $this->setId($request->id);
        }

        return $this;
    }

    public function processRequest()
    {
        if ($this->router->exist($this->getMethod())) {
            try {
                $this->methodFactory = new MethodFactory($this->router->getMethod());
            } catch (RpcExceptions $e) {
                echo 'test';exit;
                $this->setErrorMessage($e->getMessage(), $e->getCode());
                throw $e;

            }
        } else {
            echo 'test';exit;
            $this->setErrorMessage('INVALID REQUEST :: missing method', -32600);

            throw new RpcExceptions("Method {$this->getMethod()} does not exist!");
        }

        try {
            $this->methodFactory->getMethodArgs();
        } catch (RpcExceptions $e) {
            $this->setErrorMessage($e->getMessage(), $e->getCode());
            throw $e;
        }

        if ($methods = $this->router->before) {
            $this->otherRequest($methods, $this->getParams());
        }

        // if this is not an associated array we can pass it directly to the method
        // if its not lets make sure that the params are in the correct order
        try {
            if ($this->paramsAreAssoc()) {

            } else {
                $results = $this->methodFactory->executeMethod($this->getParams());
            }
        }catch (RpcExceptions $e){
            $this->setErrorMessage($e->getMessage(),$e->getCode());
            throw $e;
        }


        if ($methods = $this->router->after) {
            $this->otherRequest($methods, $this->getParams(), $results);
        }

        $this->setResult($results);

        return $results;
    }

    private function otherRequest(array $method, array $params, $results = null)
    {
        $requestParams = ['params' => $params, 'results' => $results];

        try {

            foreach ($method as $call) {
                $request = new MethodFactory($call);
                $request->getMethodArgs();
                $isTrue = $request->executeMethod($requestParams);

                if (!$isTrue) {
                    return false;
                }
            }

            return true;
        } catch (\Exception $e) {
            $this->setErrorMessage($e->getMessage(),$e->getCode());
            throw new RpcExceptions($e->getMessage(), $e->getCode());
        }
    }

    private function paramsAreAssoc()
    {
        return array_keys($this->getParams()) !== range(0, count($this->getParams()) - 1);
    }


    public function getResponse()
    {
        $response = [
            "jsonrpc" => "2.0",
        ];

        if ($this->isError()) {
            $response['error'] = $this->getErrorMessage();
        } else {
            $response['result'] = $this->getResult();
        }

        $response['id'] = $this->getId();

        return json_encode($response);
    }

} 