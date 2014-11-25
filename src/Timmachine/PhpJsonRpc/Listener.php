<?php

namespace Timmachine\PhpJsonRpc;

use Timmachine\PhpJsonRpc\Requirements;
use Timmachine\PhpJsonRpc\Router;

class Listener
{

    /**
     * @var bool
     */
    private $ValidJson = true;

    /**
     * @var
     */
    private $jsonObject;

    /**
     * @var string
     */
    private $method = '';

    /**
     * @var array
     */
    private $params = [];

    /**
     * @var int
     */
    private $id = 0;

    /**
     * @var
     */
    private $result;

    /**
     * @var bool
     */
    private $error = false;

    /**
     * @var string
     */
    private $errorMessage = '';

    /**
     * @var
     */
    private $router;

    /**
     * @var
     */
    private $methodFactory;

    /**
     * @var
     */
    private $version;

    /**
     * @var
     */
    private $requirements;

    private $methodArgs;

    /**
     * @param Router $router
     * @param string $version
     * @param array $requirements
     */
    function __construct(Router $router, $version = '2.0', array $requirements = array())
    {
        $this->setRouter($router);
        $this->setVersion($version);
        $this->setRequirements($requirements);
    }

    /**
     * @return mixed
     */
    public function getJsonObject()
    {
        return $this->jsonObject;
    }

    /**
     * @param mixed $jsonObject
     */
    public function setJsonObject($jsonObject)
    {
        $this->jsonObject = $jsonObject;
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
        if (!is_array($params)) {
            $params = [$params];
        }

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
     * @param $message
     * @param $code
     */
    public function setErrorMessage($message, $code)
    {
        $this->setError(true);
        $this->errorMessage = ['messge' => $message, 'code' => $code];
    }

    /**
     * @return mixed
     */
    public function getRequirements()
    {
        return $this->requirements;
    }

    /**
     * @param mixed $requirements
     */
    public function setRequirements($requirements)
    {
        $this->requirements = $requirements;
    }

    /**
     * @return mixed
     */
    public function getVersion()
    {
        return $this->version;
    }

    /**
     * @param mixed $version
     */
    public function setVersion($version)
    {
        $this->version = $version;
    }

    /**
     * @return mixed
     */
    public function getMethodFactory()
    {
        return $this->methodFactory;
    }

    /**
     * @param mixed $methodFactory
     */
    public function setMethodFactory(MethodFactory $methodFactory)
    {
        $this->methodFactory = $methodFactory;
    }

    /**
     * @return Router
     */
    public function getRouter()
    {
        return $this->router;
    }

    /**
     * @param Router $router
     */
    public function setRouter(Router $router)
    {
        $this->router = $router;
    }


    /**
     * @param $json
     *
     * @return $this
     */
    public function validateJson($json)
    {


        try{
            $request = json_decode($json);
        }catch (\Exception $e){
            $this->setErrorMessage($e->getMessage(),$e->getCode());
            $this->setValidJson(false);
        }

        // this should only happen if there is a json parse error due to malformed data
        if(is_null($request)){
            $this->setValidJson(false);
            $this->setErrorMessage('Json formatting is incorrect',-32700);
            $this->setError(true);
        }


        //default requirements for jsonrpc 2.0
        $requirements = new Requirements();
        $requirements->add('jsonrpc', null, 'PARSE ERROR :: missing protocol', -32700);
        $requirements->add('jsonrpc', $this->getVersion(), 'PARSE ERROR :: wrong version', -32700);
        $requirements->add('method', null, 'INVALID REQUEST :: missing method', -32600);
        $requirements->add('params', null, 'INVALID REQUEST :: missing params', -32600);

        // add the requirements


            foreach ($this->getRequirements() as $req) {
                $requirements->add($req['key'], $req['value'], $req['errorMessage'], $req['errorCode']);
            }



        // validate our requirements
        if(!$this->isError()){
            $requirements->validate($request);
        }
        // if requirement errors lets  flag them
        if ($requirements->isError()) {
            $this->setValidJson(false);
            $this->setErrorMessage($requirements->getErrorMessage(), $requirements->getErrorCode());
        }

        if ($this->isValidJson()) {
            $this->setMethod($request->method);
            $this->setParams($request->params);
            $this->setId($request->id);
            $this->setJsonObject($request);
        }

        return $this;
    }

    /**
     * @return mixed
     * @throws RpcExceptions
     * @throws \Exception
     */
    public function processRequest()
    {
        if($this->isError()){
            return null;
        }

        if ($this->router->exist($this->getMethod())) {
            try {
                $this->setMethodFactory(new MethodFactory($this->router->getMethod()));
            } catch (RpcExceptions $e) {
                $this->setErrorMessage($e->getMessage(), $e->getCode());
                throw $e;
            }
        } else {
            $this->setErrorMessage('INVALID REQUEST :: missing method', -32600);
            throw new RpcExceptions("Method {$this->getMethod()} does not exist!");
        }

        if($this->isError()){
            return null;
        }


        try {
            $this->methodArgs = $this->methodFactory->getMethodArgs();
        } catch (RpcExceptions $e) {
            $this->setErrorMessage($e->getMessage(), $e->getCode());
            throw $e;
        }

        if($this->isError()){
            return null;
        }

        if ($methods = $this->router->before) {
            $this->otherRequest($methods, $this->getParams());
        }

        if($this->isError()){
            return null;
        }

        // if this is not an associated array we can pass it directly to the method
        // if its not lets make sure that the params are in the correct order
        try {
            if (is_object($this->getParams()[0])) {
                $params = $this->orderParams();
                $results = $this->methodFactory->executeMethod($params);
            } else {
                $results = $this->methodFactory->executeMethod($this->getParams());
            }
        } catch (RpcExceptions $e) {
            $this->setErrorMessage($e->getMessage(), $e->getCode());
            throw $e;
        }

        if($this->isError()){
            return null;
        }

        if ($methods = $this->router->after) {
            $this->otherRequest($methods, $this->getParams(), $results);
        }

        if($this->isError()){
            return null;
        }

        $this->setResult($results);

        return $results;
    }

    /**
     * @param array $method
     * @param array $params
     * @param null $results
     *
     * @throws RpcExceptions
     */
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
            $this->setErrorMessage($e->getMessage(), $e->getCode());
            throw new RpcExceptions($e->getMessage(), $e->getCode());
        }
    }


    private function orderParams()
    {
        $returnData = [];
        $params = $this->getParams()[0];

        foreach ($this->methodArgs as $arg) {
            if (isset($params->{$arg->name})) {
                $returnData[$arg->name] = $params->{$arg->name};
            } elseif ($arg->isOptional()) {
                $returnData[$arg->name] = $arg->getDefaultValue();
            } else {
                throw new RpcExceptions("missing parameter :: {$arg->name}", -32602);
            }

        }

        return $returnData;
    }


    /**
     * @return string
     */
    public function getResponse()
    {
        $response = [
            "jsonrpc" => $this->getVersion(),
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