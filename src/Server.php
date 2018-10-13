<?php
declare(strict_types=1);

namespace Tehnodev\JsonRpc;

class Server
{
    protected $api = null;

    function __construct(object $api) {
        $this->api = $api;
    }
    
    public function respond(string $request)
    {
        try {
            $req = Request::parse($request);
        } catch (\InvalidArgumentException $e) {
            switch ($e->getCode()) {
                case -32600:
                    return (string) (new Error($e->getCode(), 'Invalid Request', $e->getMessage()));
                default:
                    return (string) (new Error($e->getCode(), $e->getMessage()));
            }
        }

        $class = new \ReflectionClass($this->api);
        if (!$class->hasMethod($req->method)) {
            return (string) (new Error(-32601, 'Method not found', $req->method));
        }

        $method = $class->getMethod($req->method);
        if (count($req->params) < $method->getNumberOfRequiredParameters()) {
            return (string) (new Error(-32602, 'Invalid params', 'Params count'));
        }
        if (count($req->params) > $method->getNumberOfParameters()) {
            return (string) (new Error(-32602, 'Invalid params', 'Params count'));
        }

        $params = [];
        foreach ($method->getParameters() as $param) {
            if (isset($req->params[$param->getPosition()])) {
                $params[] = $req->params[$param->getPosition()];
            } elseif (isset($req->params[$param->getName()])) {
                $params[] = $req->params[$param->getName()];
            } else {
                if ($param->isDefaultValueAvailable()) {
                    $params[] = $param->getDefaultValue();
                } else {
                    return (string) (new Error(-32602, 'Invalid params'));
                }
            }
        }

        $m = $req->method;
        $resp = $this->api->$m(...$params);

        return (string) (new Response($req->id, $resp));
    }
}
