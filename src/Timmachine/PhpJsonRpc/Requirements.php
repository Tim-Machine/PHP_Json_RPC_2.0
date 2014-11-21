<?php namespace Timmachine\PhpJsonRpc;

/**
 * Class Requirements
 * @package Timmachine\PhpJsonRpc
 */
class Requirements
{

    /**
     * @var array
     */
    private $requirements = [];

    /**
     * @var string
     */
    private $errorMessage = '';

    /**
     * @var int
     */
    private $errorCode = 0;

    /**
     * @var bool
     */
    private $error = false;

    /**
     * @return array
     */
    public function getRequirements()
    {
        return $this->requirements;
    }

    /**
     * @param object $requirements
     */
    public function setRequirements($requirements)
    {
        array_push($this->requirements, $requirements);
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
     * @return string
     */
    public function getErrorMessage()
    {
        return $this->errorMessage;
    }

    /**
     * @param string $errorMessage
     */
    public function setErrorMessage($errorMessage, $errorcode)
    {
        $this->setError(true);
        $this->errorMessage = $errorMessage;
        $this->setErrorCode($errorcode);
    }

    /**
     * @return int
     */
    public function getErrorCode()
    {
        return $this->errorCode;
    }

    /**
     * @param int $errorCode
     */
    public function setErrorCode($errorCode)
    {
        $this->errorCode = $errorCode;
    }


    /**
     * @param $key
     * @param $value
     * @param $errorMessage
     * @param $errorCode
     *
     * @return \stdClass
     */
    public function add($key, $value, $errorMessage, $errorCode)
    {

        $requirement               = new \stdClass();
        $requirement->key          = $key;
        $requirement->value        = $value;
        $requirement->errorMessage = $errorMessage;
        $requirement->errorCode    = $errorCode;

        $this->setRequirements($requirement);

        return $requirement;
    }


    /**
     * @param $request
     */
    public function validate($request)
    {
        foreach ($this->getRequirements() as $requirement) {
            if ($this->isError()) {
                continue;
            }

            if (!isset($request->{$requirement->key})) {
                $this->setErrorMessage($requirement->errorMessage, $requirement->errorCode);
            }

            if (!is_null($requirement->value) && $requirement->value !== $request->{$requirement->key}) {
                $this->setErrorMessage($requirement->errorMessage, $requirement->errorCode);
            }
        }
    }


}