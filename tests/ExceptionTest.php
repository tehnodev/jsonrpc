<?php

use PHPUnit\Framework\TestCase;
use Tehnodev\JsonRpc\Exception as JsonRpcException;

class ExceptionTest extends TestCase
{
    function testException() {
        try {
            throw new JsonRpcException('Foo');
        } catch (JsonRpcException $e) {
            $this->assertEquals('Foo', $e->getMessage());
            $this->assertEquals(0, $e->getCode());
            $this->assertNull($e->getData());
        }
        
        try {
            throw new JsonRpcException('Foo', 123, ['foo' => 'bar']);
        } catch (JsonRpcException $e) {
            $this->assertEquals('Foo', $e->getMessage());
            $this->assertEquals(123, $e->getCode());
            $this->assertEquals(['foo' => 'bar'], $e->getData());
        }
    }
}