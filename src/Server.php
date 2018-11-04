<?php
declare(strict_types=1);

namespace Tehnodev\JsonRpc;

class Server
{
    protected $api = null;

    public function __construct(object $api)
    {
        $this->api = $api;
    }
    
    public function respond(string $request)
    {
        $req = json_decode($request);
        if ($req === null) {
            return (string) new Error(null, -32700, 'Parse error');
        }

        if (is_object($req)) {
            try {
                return $this->handle(new Request($req));
            } catch (\InvalidArgumentException $e) {
                return (string) new Error(null, $e->getCode(), 'Invalid Request', $e->getMessage());
            }
        }

        if (is_array($req)) {
            if (count($req) === 0) {
                return (string) new Error(null, -32600, 'Invalid Request');
            }
            
            $results = [];
            foreach ($req as $r) {
                if (!is_object($r)) {
                    $results[] = (string) new Error(null, -32600, 'Invalid Request');
                    continue;
                }
                try {
                    $res = $this->handle(new Request($r));
                    if ($res === '') {
                        continue;
                    }
                    $results[] = $res;
                } catch (\InvalidArgumentException $e) {
                    $results[] = (string) new Error(null, $e->getCode(), 'Invalid Request', $e->getMessage());
                }
            }
            return count($results) === 0 ? '' : '['.join(',', $results).']';
        }

        return (string) new Error(null, -32600, 'Invalid Request');
    }

    protected function handle(Request $req)
    {
        $class = new \ReflectionClass($this->api);
        if (!$class->hasMethod($req->method)) {
            if ($req->id === null) {
                return '';
            }
            
            return (string) (new Error($req->id, -32601, 'Method not found', $req->method));
        }

        $method = $class->getMethod($req->method);
        if (count($req->params) < $method->getNumberOfRequiredParameters()) {
            if ($req->id === null) {
                return '';
            }
            
            return (string) (new Error($req->id, -32602, 'Invalid params', 'Params count'));
        }
        if (count($req->params) > $method->getNumberOfParameters()) {
            if ($req->id === null) {
                return '';
            }
            
            return (string) (new Error($req->id, -32602, 'Invalid params', 'Params count'));
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
                    if ($req->id === null) {
                        return '';
                    }
                    
                    return (string) (new Error($req->id, -32602, 'Invalid params'));
                }
            }
        }

        $m = $req->method;
        try {
            $resp = $this->api->$m(...$params);
        } catch (Exception $e) {
            if ($req->id === null) {
                return '';
            }

            $data = $e->getData();
            if ($data === null) {
                return (string) (new Error($req->id, $e->getCode(), $e->getMessage()));
            }
            return (string) (new Error($req->id, $e->getCode(), $e->getMessage(), $data));
        } catch (\Exception $e) {
            if ($req->id === null) {
                return '';
            }

            return (string) (new Error($req->id, $e->getCode(), $e->getMessage()));
        }

        if ($req->id === null) {
            return '';
        }
        
        return (string) (new Response($req->id, $resp));
    }
}
