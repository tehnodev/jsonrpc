<?php
declare(strict_types=1);

namespace Tehnodev\JsonRpc;

class Request
{
    protected $method = '';
    protected $params = [];
    protected $id = null;

    public function __construct(object $data)
    {
        // version
        if (!isset($data->jsonrpc) || $data->jsonrpc !== '2.0') {
            throw new \InvalidArgumentException('jsonrpc', -32600);
        }

        // method
        if (!isset($data->method)) {
            throw new \InvalidArgumentException('method', -32600);
        }
        if (!is_string($data->method)) {
            throw new \InvalidArgumentException('method', -32600);
        }
        if ($data->method === '') {
            throw new \InvalidArgumentException('method', -32600);
        }
        if (strtolower(substr($data->method, 0, 4)) == 'rpc.') {
            throw new \InvalidArgumentException('method', -32600);
        }
        $this->method = $data->method;

        // params
        if (isset($data->params)) {
            if (is_object($data->params)) {
                $this->params = (array) $data->params;
            } elseif (is_array($data->params)) {
                $this->params = $data->params;
            } else {
                throw new \InvalidArgumentException('params', -32600);
            }
        }

        // id
        if (isset($data->id)) {
            if (!($data->id === null || is_string($data->id) || is_int($data->id))) {
                throw new \InvalidArgumentException('id', -32600);
            }
            $this->id = $data->id;
        }
    }

    public function __get($name)
    {
        switch ($name) {
            case 'method':
                return $this->method;
            case 'params':
                return $this->params;
            case 'id':
                return $this->id;
            default:
                throw new \Exception('Undefined property '.$name);
        }
    }
}
