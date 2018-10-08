<?php

namespace Tehnodev\JsonRpc;

class Error
{
    protected $code;
    protected $message;
    protected $data;

    public function __construct($code, $message, $data = null)
    {
        $this->code = $code;
        $this->message = $message;
        $this->data = $data;
    }

    public function __toString()
    {
        $err = [
            'jsonrpc' => '2.0',
            'error' => [
                'code' => $this->code,
                'message' => $this->message
            ]
        ];

        if ($this->data !== null) {
            $err['error']['data'] = $this->data;
        }

        return json_encode($err);
    }
}
