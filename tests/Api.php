<?php

namespace Tehnodev\JsonRpc\Test;

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

    function throwEx() {
        throw new \Exception('FooBar', 123, ['foo' => 'bar']);
    }
}