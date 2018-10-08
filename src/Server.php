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
        if (!$class->hasMethod($req->method())) {
            return (string) (new Error(-32601, 'Method not found', $req->method()));
        }

        // $refl = [];
        // $class = new \ReflectionClass($this->api);
        // foreach ($class->getMethods() as $method) {
        //     foreach ($method->getParameters() as $param) {
        //         $refl[$method->name][$param->getName()] = [
        //             'type' => (string) $param->getType(),
        //             'position' => $param->getPosition(),
        //             'optional' => $param->isOptional()
        //         ];

        //         if ($param->isDefaultValueAvailable()) {
        //             $refl[$method->name][$param->getName()]['default'] = $param->getDefaultValue();
        //         }
        //     }
        // }
        // print_r($refl);
    }
}
