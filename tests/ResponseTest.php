<?php

use PHPUnit\Framework\TestCase;
use Tehnodev\JsonRpc\Response;

class ResponseTest extends TestCase
{
    public function testConstruct()
    {
        $res = new Response(null);
        $this->assertInstanceOf(Response::class, $res);

        $res = new Response(1, 'sum');
        $this->assertInstanceOf(Response::class, $res);
    }

    public function testStringConversion()
    {
        $res = new Response(null);
        $data = json_decode($res);
        $this->assertEquals('2.0', $data->jsonrpc);
        $this->assertNull($data->result);
        $this->assertFalse(property_exists($data, 'id'));

        $res = new Response(1, ['foo', 'bar']);
        $data = json_decode($res);
        $this->assertEquals('2.0', $data->jsonrpc);
        $this->assertCount(2, $data->result);
        $this->assertEquals(1, $data->id);
    }
}
