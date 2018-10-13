<?php

namespace Tehnodev\JsonRpc\Test;

use Tehnodev\JsonRpc\Exception as JsonRpcException;

class Api
{
    public function noParams()
    {
    }

    public function oneParam($foo)
    {
        return $foo;
    }

    public function optParam($foo = 'foo')
    {
        return $foo;
    }

    public function twoParams($foo, $bar)
    {
        return ['foo' => $foo, 'bar' => $bar];
    }

    public function twoParamsWithOpt($foo, $bar, $baz = 'baz')
    {
        return ['foo' => $foo, 'bar' => $bar, 'baz' => $baz];
    }

    public function typedParams(int $foo, string $bar, array $baz = [])
    {
        return ['foo' => $foo, 'bar' => $bar, 'baz' => $baz];
    }

    public function throwException()
    {
        throw new \Exception('Foo');
    }

    public function throwExceptionWithCode()
    {
        throw new \Exception('Foo', 123);
    }

    public function throwExceptionWithData()
    {
        throw new JsonRpcException('FooBar', 123, ['foo' => 'bar']);
    }
}
