<?php

namespace Tehnodev\JsonRpc\Test;

use Tehnodev\JsonRpc\Exception as JsonRpcException;

class Api {
    function noParams() {
    }

    function oneParam($foo) {
        return $foo;
    }

    function optParam($foo = 'foo') {
        return $foo;
    }

    function twoParams($foo, $bar) {
        return ['foo' => $foo, 'bar' => $bar];
    }

    function twoParamsWithOpt($foo, $bar, $baz = 'baz') {
        return ['foo' => $foo, 'bar' => $bar, 'baz' => $baz];
    }

    function typedParams(int $foo, string $bar, array $baz = []) {
        return ['foo' => $foo, 'bar' => $bar, 'baz' => $baz];
    }

    function throwException() {
        throw new \Exception('Foo');
    }

    function throwExceptionWithCode() {
        throw new \Exception('Foo', 123);
    }

    function throwExceptionWithData() {
        throw new JsonRpcException('FooBar', 123, ['foo' => 'bar']);
    }
}