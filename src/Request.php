<?php

namespace Tehnodev\JsonRpc;

class Request
{
    protected $id = null;
    protected $method = '';
    protected $params = array();

    public function __construct($id, $method, $params = array())
    {
        if (!self::isValidId($id)) {
            throw new \InvalidArgumentException('id');
        }
        
        if (!self::isValidMethod($method)) {
            throw new \InvalidArgumentException('method');
        }

        if (!self::isValidParams($params)) {
            throw new \InvalidArgumentException('params');
        }

        $this->id = $id;
        $this->method = $method;
        $this->params = $params;
    }

    public static function parse($data)
    {
        $req = json_decode($data, true);

        if ($req === null) {
            throw new \InvalidArgumentException('Parse error', -32700);
        }

        if (empty($req['jsonrpc']) || $req['jsonrpc'] !== '2.0') {
            throw new \InvalidArgumentException('jsonrpc', -32600);
        }

        if (empty($req['method'])) {
            throw new \InvalidArgumentException('method', -32600);
        }
        if (!self::isValidMethod($req['method'])) {
            throw new \InvalidArgumentException('method', -32600);
        }
        
        if (array_key_exists('params', $req)) {
            if (!self::isValidParams($req['params'])) {
                throw new \InvalidArgumentException('params', -32600);
            }
        }

        if (array_key_exists('id', $req)) {
            if (!self::isValidId($req['id'])) {
                throw new \InvalidArgumentException('id', -32600);
            }
        }

        $id = array_key_exists('id', $req) ? $req['id'] : null;
        $params = isset($req['params']) ? $req['params'] : array();

        return new self($id, $req['method'], $params);
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

    protected static function isValidMethod($name) {
        if (!is_string($name)) {
            return false;
        }
        if (strtolower(substr($name, 0, 4)) == 'rpc.') {
            return false;
        }
        
        return true;
    }
    
    protected static function isValidParams($params) {
        return is_array($params);
    }

    protected static function isValidId($id) {
        return $id === null || is_string($id) || is_int($id);
    }
}
