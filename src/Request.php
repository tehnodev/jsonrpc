<?php

namespace Tehnodev\JsonRpc;

class Request
{
    protected $id = null;
    protected $method = '';
    protected $params = array();

    public function __construct($id, $method, $params = array())
    {
        $this->id = $id;
        $this->method = $method;
        $this->params = $params;
    }

    public static function parse($data)
    {
        $req = json_decode($data);

        if ($req === null) {
            throw new \InvalidArgumentException('Parse error', -32700);
        }

        if (empty($req->jsonrpc) || $req->jsonrpc !== '2.0') {
            throw new \InvalidArgumentException('jsonrpc', -32600);
        }

        if (empty($req->method)) {
            throw new \InvalidArgumentException('method', -32600);
        }
        if (!is_string($req->method)) {
            throw new \InvalidArgumentException('method', -32600);
        }
        if (strtolower(substr($req->method, 0, 4)) == 'rpc.') {
            throw new \InvalidArgumentException('method', -32600);
        }

        if (property_exists($req, 'id')) {
            if (!($req->id === null || is_string($req->id) || is_int($req->id))) {
                throw new \InvalidArgumentException('id', -32600);
            }
        }

        if (isset($req->params)) {
            if (!is_array($req->params)) {
                throw new \InvalidArgumentException('params', -32600);
            }
        }

        $id = property_exists($req, 'id') ? $req->id : null;
        $params = isset($req->params) ? $req->params : array();

        return new self($id, $req->method, $params);
    }

    function id() {
        return $this->id;
    }

    function method() {
        return $this->method;
    }

    function params() {
        return $this->params;
    }

    public function __toString()
    {
        $req = [
            'jsonrpc' => '2.0',
            'method' => $this->method,
        ];

        if (!empty($this->params)) {
            $req['params'] = $this->params;
        }

        if ($this->id !== null) {
            $req['id'] = $this->id;
        }

        return json_encode($req);
    }
}
