<?php
/**
 * Created by PhpStorm.
 * User: Timothy
 * Date: 11/18/2014
 * Time: 9:56 PM
 */

namespace Timmachine\PhpJsonRpc;


class Requirements {

    public static function add($key, $value, $errorMessage, $errorCode){

        $requirement = new \stdClass();
        $requirement->key = $key;
        $requirement->value = $value;
        $requirement->errorMessage = $errorMessage;
        $requirement->errorCode = $errorCode;

        return $requirement;
    }
} 