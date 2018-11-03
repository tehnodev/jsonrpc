<?php

namespace Tehnodev\JsonRpc;

class Error
{
    protected $id = null;
    protected $code;
    protected $message;
    protected $data;

    public function __construct($id, $code, $message, $data = null)
    {
        $this->id = $id;
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
            ],
            'id' => $this->id
        ];

        if ($this->data !== null) {
            $err['error']['data'] = $this->data;
        }

        return json_encode($err);
    }
}
