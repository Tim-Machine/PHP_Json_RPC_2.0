<?php
/**
 * Created by PhpStorm.
 * User: Timothy
 * Date: 11/19/2014
 * Time: 8:13 PM
 */

namespace Timmachine\PhpJsonRpc;


class RouteFactory {

    public $name;
    public $method;
    public $before;
    public $after;


    function __construct( $name, $method, $before ,$after)
    {
        $this->setName($name);
        $this->setMethod($method);
        $this->setBefore($before);
        $this->setAfter($after);
    }

    /**
     * @return mixed
     */
    public function getAfter()
    {
        return $this->after;
    }

    /**
     * @param mixed $after
     */
    public function setAfter($after)
    {
        $this->after = $after;
    }

    /**
     * @return mixed
     */
    public function getBefore()
    {
        return $this->before;
    }

    /**
     * @param mixed $before
     */
    public function setBefore($before)
    {
        $this->before = $before;
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
     * @return mixed
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param mixed $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }



} 