<?php

use PHPUnit\Framework\TestCase;
use Tehnodev\JsonRpc\Request;

class RequestTest extends TestCase
{
    public function testConstruct()
    {
        $req = new Request((object)[
            'jsonrpc' => '2.0',
            'method' => 'sum'
        ]);
        $this->assertInstanceOf(Request::class, $req);

        $req = new Request((object)[
            'jsonrpc' => '2.0',
            'method' => 'sum',
            'id' => null
        ]);
        $this->assertInstanceOf(Request::class, $req);

        $req = new Request((object)[
            'jsonrpc' => '2.0',
            'method' => 'sum',
            'params' => [1, 2],
            'id' => 1
        ]);
        $this->assertInstanceOf(Request::class, $req);
    }
        
    public function testConstructNoJsonrpcParam()
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionCode(-32600);
        $this->expectExceptionMessage('jsonrpc');
        new Request((object)[
            'method' => 'sum'
        ]);
    }

    public function testConstructInvalidJsonrpcVersion()
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionCode(-32600);
        $this->expectExceptionMessage('jsonrpc');
        new Request((object)[
            'jsonrpc' => '1.0',
            'method' => 'sum'
        ]);
    }

    public function testConstructInvalidId()
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('id');
        new Request((object)[
            'jsonrpc' => '2.0',
            'method' => 'add',
            'id' => 1.2
        ]);
    }

    public function testConstructInvalidMethod()
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('method');
        new Request((object)[
            'jsonrpc' => '2.0',
            'method' => 1
        ]);
    }

    public function testConstructNoMethodParam()
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionCode(-32600);
        $this->expectExceptionMessage('method');
        new Request((object)[
            'jsonrpc' => '2.0'
        ]);
    }

    public function testConstructReservedMethodPrefix()
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionCode(-32600);
        $this->expectExceptionMessage('method');
        new Request((object)[
            'jsonrpc' => '2.0',
            'method' => 'RPC.sum'
        ]);
    }

    public function testConstructInvalidParams()
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('params');
        new Request((object)[
            'jsonrpc' => '2.0',
            'method' => 'add',
            'params' => '1,2'
        ]);
    }
}
