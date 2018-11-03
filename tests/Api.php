<?php

namespace Tehnodev\JsonRpc\Test;

use Tehnodev\JsonRpc\Exception as JsonRpcException;

class Api
{
    function subtract($minuend, $subtrahend) {
        return $minuend - $subtrahend;
    }

    function sum($a, $b, $c) {
        return $a + $b + $c;
    }

    function update($a, $b, $c, $d, $e) {}

    function foobar() {}

    function get_data() {
        return ['hello', 5];
    }

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
