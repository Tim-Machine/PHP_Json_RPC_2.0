<?php
/**
 * Created by PhpStorm.
 * User: Timothy
 * Date: 11/18/2014
 * Time: 9:00 PM
 */

namespace Timmachine\PhpJsonRpc;


class RpcExceptions extends  \Exception {
    // Redefine the exception so message isn't optional
    public function __construct($message, $code = 0, Exception $previous = null) {
        // make sure everything is assigned properly
        parent::__construct($message, $code, $previous);
    }

    // custom string representation of object
    public function __toString() {
        return __CLASS__ . ": [{$this->code}]: {$this->message}\n";
    }

} 