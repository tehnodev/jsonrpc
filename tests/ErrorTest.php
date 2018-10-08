<?php

use PHPUnit\Framework\TestCase;
use Tehnodev\JsonRpc\Error;

class ErrorTest extends TestCase
{
    public function testConstruct()
    {
        $err = new Error(-32700, 'Parse error');
        $this->assertInstanceOf(Error::class, $err);

        $err = new Error(-32700, 'Parse error', [':(']);
        $this->assertInstanceOf(Error::class, $err);
    }

    public function testStringConversion()
    {
        $err = new Error(-32700, 'Parse error');
        $data = json_decode($err);
        $this->assertEquals('2.0', $data->jsonrpc);
        $this->assertEquals(-32700, $data->error->code);
        $this->assertEquals('Parse error', $data->error->message);
        $this->assertFalse(property_exists($data->error, 'data'));

        $err = new Error(-32700, 'Parse error', [':(']);
        $data = json_decode($err);
        $this->assertEquals('2.0', $data->jsonrpc);
        $this->assertEquals(-32700, $data->error->code);
        $this->assertEquals('Parse error', $data->error->message);
        $this->assertCount(1, $data->error->data);
    }
}
