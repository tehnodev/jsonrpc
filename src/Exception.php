<?php

namespace Tehnodev\JsonRpc;

class Exception extends \Exception {
    protected $data = null;

    function __construct($message, $code = 0, $data = null) {
        parent::__construct($message, $code);
        $this->data = $data;
    }

    function getData() {
        return $this->data;
    }
}