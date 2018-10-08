<?php

use PHPUnit\Framework\TestCase;
use Tehnodev\JsonRpc\Request;

class RequestTest extends TestCase
{
    public function testConstruct()
    {
        $req = new Request(null, 'sum');
        $this->assertInstanceOf(Request::class, $req);

        $req = new Request(1, 'sum', [1, 2]);
        $this->assertInstanceOf(Request::class, $req);
    }

    public function testParse()
    {
        $req = Request::parse(json_encode([
            'jsonrpc' => '2.0',
            'method' => 'sum'
        ]));
        $this->assertInstanceOf(Request::class, $req);

        $req = Request::parse(json_encode([
            'jsonrpc' => '2.0',
            'method' => 'sum',
            'params' => [1, 2]
        ]));
        $this->assertInstanceOf(Request::class, $req);

        $req = Request::parse(json_encode([
            'jsonrpc' => '2.0',
            'method' => 'sum',
            'params' => [1, 2],
            'id' => 1
        ]));
        $this->assertInstanceOf(Request::class, $req);

        $req = Request::parse(json_encode([
            'jsonrpc' => '2.0',
            'method' => 'sum',
            'params' => [1, 2],
            'id' => 'abc'
        ]));
        $this->assertInstanceOf(Request::class, $req);

        $req = Request::parse(json_encode([
            'jsonrpc' => '2.0',
            'method' => 'sum',
            'params' => [1, 2],
            'id' => null
        ]));
        $this->assertInstanceOf(Request::class, $req);
    }

    public function testParse_InvalidJson()
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionCode(-32700);
        $req = Request::parse('');
    }
    
    public function testParse_NoJsonrpcParam()
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionCode(-32600);
        $this->expectExceptionMessage('jsonrpc');
        $req = Request::parse(json_encode([
            'method' => 'sum'
        ]));
    }

    public function testParse_InvalidJsonrpcVersion()
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionCode(-32600);
        $this->expectExceptionMessage('jsonrpc');
        $req = Request::parse(json_encode([
            'jsonrpc' => '1.0',
            'method' => 'sum'
        ]));
    }

    public function testParse_NoMethodParam()
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionCode(-32600);
        $this->expectExceptionMessage('method');
        $req = Request::parse(json_encode([
            'jsonrpc' => '2.0'
        ]));
    }

    public function testParse_ReservedMethodPrefix()
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionCode(-32600);
        $this->expectExceptionMessage('method');
        $req = Request::parse(json_encode([
            'jsonrpc' => '2.0',
            'method' => 'RPC.sum'
        ]));
    }

    public function testParse_MethodNameNotString()
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionCode(-32600);
        $this->expectExceptionMessage('method');
        $req = Request::parse(json_encode([
            'jsonrpc' => '2.0',
            'method' => 123
        ]));
    }

    public function testParse_ParamsNotArray()
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionCode(-32600);
        $this->expectExceptionMessage('params');
        $req = Request::parse(json_encode([
            'jsonrpc' => '2.0',
            'method' => 'sum',
            'params' => '1;2'
        ]));
    }

    public function testParse_InvalidId()
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionCode(-32600);
        $this->expectExceptionMessage('id');
        $req = Request::parse(json_encode([
            'jsonrpc' => '2.0',
            'method' => 'sum',
            'id' => 1.2
        ]));
    }

    function testId() {
        $req = new Request(null, 'add');
        $this->assertNull($req->id());

        $req = new Request(10, 'add');
        $this->assertEquals(10, $req->id());
    }

    function testMethod() {
        $req = new Request(1, 'add');
        $this->assertEquals('add', $req->method());
    }

    function testParams() {
        $req = new Request(1, 'add');
        $this->assertEmpty($req->params());
        
        $req = new Request(1, 'add', '1');
        $this->assertEquals('1', $req->params());
    }

    public function testStringConversion()
    {
        $req = new Request(1, 'sum');
        $data = json_decode($req);
        $this->assertEquals('2.0', $data->jsonrpc);
        $this->assertEquals('sum', $data->method);
        $this->assertFalse(property_exists($data, 'params'));

        $req = new Request(null, 'sum', [1, 2]);
        $data = json_decode($req);
        $this->assertEquals('2.0', $data->jsonrpc);
        $this->assertEquals('sum', $data->method);
        $this->assertCount(2, $data->params);
        $this->assertFalse(property_exists($data, 'id'));
    }
}
