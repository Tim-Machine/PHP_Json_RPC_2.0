<?php

namespace Timmachine\PhpJsonRpc;

/**
 * Class Router
 * @package Timmachine\PhpJsonRpc
 */
class Router
{

    /**
     * array of possible routes
     * @var array
     */
    private $routes = [];

    public $method;

    public $before;
    public $after;

    /**
     *  adds a route to the routes array
     *
     * @param      $name
     * @param      $method
     * @param null $before
     * @param null $after
     */
    public function add($name, $method, array $before = null, array $after = null)
    {
        $route = new RouteFactory($name, $method, $before, $after);
        array_push($this->routes, $route);
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
     * checks to see if a route exist
     *
     * @param string $name
     *
     * @return bool
     */
    public function exist($name)
    {
        $exist = false;

        for ($i = 0; $i <= count($this->routes) - 1; $i++) {
            if ($exist) {
                return $exist;
            }
            if ($this->routes[$i]->name === $name) {
                $exist = true;
                $this->setMethod($this->routes[$i]->method);
                $this->setBefore($this->routes[$i]->before);
                $this->setAfter($this->routes[$i]->after);
            }
        }

        return $exist;
    }


} 