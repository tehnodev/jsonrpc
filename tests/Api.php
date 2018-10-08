<?php

namespace Tehnodev\JsonRpc\Test;

class Api {
    function noParams() {
    }

    function oneParam($foo) {
        return $foo;
    }

    function twoParams($foo, $bar) {
        return [$foo, $bar];
    }

    function optParam($foo = 'foo') {
        return $foo;
    }

    function twoParamsWithOpt($foo, $bar, $baz = 'baz') {
        return [$foo, $bar, $baz];
    }

    function typedParams(int $foo, string $bar, array $baz = []) {
        return [$foo, $bar, $baz];
    }
}