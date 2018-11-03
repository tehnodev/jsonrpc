<?php

use PHPUnit\Framework\TestCase;
use Tehnodev\JsonRpc\Error;

class ErrorTest extends TestCase
{
    public function testStringConversionWithoutData()
    {
        $err = new Error(1, -32700, 'Parse error');
        $data = json_decode($err);
        $this->assertEquals('2.0', $data->jsonrpc);
        $this->assertTrue($data->error->code === -32700);
        $this->assertEquals('Parse error', $data->error->message);
        $this->assertFalse(property_exists($data->error, 'data'));
        $this->assertTrue($data->id === 1);
    }

    function testStringConversionWithPrimitiveData() {
        $err = new Error(null, -32700, 'Parse error', 'foo');
        $data = json_decode($err);
        $this->assertEquals('2.0', $data->jsonrpc);
        $this->assertTrue($data->error->code === -32700);
        $this->assertEquals('Parse error', $data->error->message);
        $this->assertEquals('foo', $data->error->data);
        $this->assertTrue($data->id === null);
    }

    function testStringConversionWithStructuredData() {
        $err = new Error(null, -32700, 'Parse error', ['foo' => 'bar']);
        $data = json_decode($err);
        $this->assertEquals('2.0', $data->jsonrpc);
        $this->assertTrue($data->error->code === -32700);
        $this->assertEquals('Parse error', $data->error->message);
        $this->assertEquals((object)['foo' => 'bar'], $data->error->data);
        $this->assertTrue($data->id === null);
    }
}
