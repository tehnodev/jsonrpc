<?php
declare(strict_types=1);

namespace Tehnodev\JsonRpc;

class Response
{
    protected $result;
    protected $id;

    public function __construct($id, $data = null)
    {
        $this->result = $data;
        $this->id = $id;
    }

    public function __toString()
    {
        $resp = [
            'jsonrpc' => '2.0',
            'result' => $this->result
        ];

        if ($this->id !== null) {
            $resp['id'] = $this->id;
        }

        return json_encode($resp);
    }
}
