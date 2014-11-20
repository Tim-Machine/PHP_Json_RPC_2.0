<?php

namespace Timmachine\PhpJsonRpc;

use Timmachine\PhpJsonRpc\Requirements;
use Timmachine\PhpJsonRpc\Router;

class Listener
{

    protected $ValidJson = true;

    protected $method = '';

    protected $params = [];

    protected $id = 0;

    protected $result = [];

    protected $error = '';

    private $router;

    function __construct(Router $router)
    {
        $this->router = $router;
    }

    /**
     * @return string
     */
    public function getError()
    {
        return $this->error;
    }

    /**
     * @param string $error
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
     * @param array $result
     */
    public function setResult($result)
    {
        $this->result = $result;
    }


    public function validateJson($json)
    {
        $request = json_decode($json);

        $requirements = [
            'protocol' => Requirements::add('jsonrpc', null, 'PARSE ERROR :: missing protocol', -32700),
            'version'  => Requirements::add('jsonrpc', "2.0", 'PARSE ERROR :: wrong version', -32700),
            'method'   => Requirements::add('method', null, 'INVALID REQUEST :: missing method', -32600),
            'params'   => Requirements::add('params', null, 'INVALID REQUEST :: missing params', -32600),
        ];

        foreach ($requirements as $requirement) {
            if (!$this->isValidJson()) {
                continue;
            }

            if (!isset($request->{$requirement->key})) {
                $this->setValidJson(false);
                $this->setError(['message' => $requirement->errorMessage, 'code' => $requirement->errorCode]);
                throw new RpcExceptions($requirement->errorMessage, $requirement->errorCode);
                continue;
            }

            if (!is_null($requirement->value) && $requirement->value !== $request->{$requirement->key}) {
                $this->setValidJson(false);
                $this->setError(['message' => $requirement->errorMessage, 'code' => $requirement->errorCode]);
                throw new RpcExceptions($requirement->errorMessage, $requirement->errorCode);
                continue;
            }
        }

        if ($this->isValidJson()) {

            $this->setMethod($request->method);
            $this->setParams($request->params);
            $this->setId($request->id);
        }

        return $this->isValidJson();
    }

    public function processRequest()
    {
        print('Route:: '.$this->getMethod());

        $exist = $this->router->exist($this->getMethod());
        var_dump($exist);

    }

} 