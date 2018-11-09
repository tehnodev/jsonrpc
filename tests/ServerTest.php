<?php
declare(strict_types=1);

use PHPUnit\Framework\TestCase;
use Tehnodev\JsonRpc\Server;
use Tehnodev\JsonRpc\Request;
use Tehnodev\JsonRpc\Test\Api;

class ServerTest extends TestCase
{
    public function testRespondErrors()
    {
        $server = new Server(new Api());

        $res = json_decode($server->respond('foo'));
        $this->assertEquals(-32700, $res->error->code);
        $this->assertEquals('Parse error', $res->error->message);
        
        $res = json_decode($server->respond(json_encode(['foo' => 'bar'])));
        $this->assertEquals(-32600, $res->error->code);
        $this->assertEquals('Invalid Request', $res->error->message);

        $res = json_decode($server->respond(json_encode('foo')));
        $this->assertEquals(-32600, $res->error->code);
        $this->assertEquals('Invalid Request', $res->error->message);

        $res = json_decode($server->respond(json_encode([
            'jsonrpc' => '2.0',
            'method' => 'add',
            'id' => 1
        ])));
        $this->assertEquals(-32601, $res->error->code);
        $this->assertEquals('Method not found', $res->error->message);
        $this->assertEquals('add', $res->error->data);

        $res = json_decode($server->respond(json_encode([
            'jsonrpc' => '2.0',
            'method' => 'oneParam',
            'id' => 1
        ])));
        $this->assertEquals(-32602, $res->error->code);
        $this->assertEquals('Invalid params', $res->error->message);
        $this->assertEquals('Params count', $res->error->data);

        $res = json_decode($server->respond(json_encode([
            'jsonrpc' => '2.0',
            'method' => 'twoParams',
            'params' => ['foo'],
            'id' => 1
        ])));
        $this->assertEquals(-32602, $res->error->code);
        $this->assertEquals('Invalid params', $res->error->message);
        $this->assertEquals('Params count', $res->error->data);

        $res = json_decode($server->respond(json_encode([
            'jsonrpc' => '2.0',
            'method' => 'oneParam',
            'params' => ['foo', 'bar'],
            'id' => 1
        ])));
        $this->assertEquals(-32602, $res->error->code);
        $this->assertEquals('Invalid params', $res->error->message);
        $this->assertEquals('Params count', $res->error->data);

        $res = json_decode($server->respond(json_encode([
            'jsonrpc' => '2.0',
            'method' => 'twoParamsWithOpt',
            'params' => ['foo' =>'bar', 'baz' => 'foo'],
            'id' => 1
        ])));
        $this->assertEquals(-32602, $res->error->code);
        $this->assertEquals('Invalid params', $res->error->message);
    }

    public function testApi()
    {
        $server = new Server(new Api());
        
        $res = $server->respond(json_encode([
            'jsonrpc' => '2.0',
            'method' => 'noParams',
            'id' => 123
        ]));
        $data = json_decode($res);
        $this->assertNull($data->result);
        $this->assertTrue($data->id === 123);
        
        $res = $server->respond(json_encode([
            'jsonrpc' => '2.0',
            'method' => 'oneParam',
            'params' => ['foo'],
            'id' => 123
        ]));
        $data = json_decode($res);
        $this->assertEquals('foo', $data->result);

        $res = $server->respond(json_encode([
            'jsonrpc' => '2.0',
            'method' => 'oneParam',
            'params' => ['foo' => 'bar'],
            'id' => 123
        ]));
        $data = json_decode($res);
        $this->assertEquals('bar', $data->result);

        $res = $server->respond(json_encode([
            'jsonrpc' => '2.0',
            'method' => 'optParam',
            'id' => 123
        ]));
        $data = json_decode($res);
        $this->assertEquals('foo', $data->result);

        $res = $server->respond(json_encode([
            'jsonrpc' => '2.0',
            'method' => 'optParam',
            'params' => ['bar'],
            'id' => 123
        ]));
        $data = json_decode($res);
        $this->assertEquals('bar', $data->result);

        $res = $server->respond(json_encode([
            'jsonrpc' => '2.0',
            'method' => 'optParam',
            'params' => ['foo' => 'bar'],
            'id' => 123
        ]));
        $data = json_decode($res);
        $this->assertEquals('bar', $data->result);

        $res = $server->respond(json_encode([
            'jsonrpc' => '2.0',
            'method' => 'twoParams',
            'params' => ['bar', 'foo'],
            'id' => 123
        ]));
        $data = json_decode($res);
        $this->assertEquals('bar', $data->result->foo);
        $this->assertEquals('foo', $data->result->bar);

        $res = $server->respond(json_encode([
            'jsonrpc' => '2.0',
            'method' => 'twoParams',
            'params' => ['foo' => 'bar', 'bar' => 'foo'],
            'id' => 123
        ]));
        $data = json_decode($res);
        $this->assertEquals('bar', $data->result->foo);
        $this->assertEquals('foo', $data->result->bar);

        $res = $server->respond(json_encode([
            'jsonrpc' => '2.0',
            'method' => 'twoParamsWithOpt',
            'params' => ['bar' => 'foo', 'foo' => 'bar'],
            'id' => 123
        ]));
        $data = json_decode($res);
        $this->assertEquals('bar', $data->result->foo);
        $this->assertEquals('foo', $data->result->bar);
        $this->assertEquals('baz', $data->result->baz);

        $res = $server->respond(json_encode([
            'jsonrpc' => '2.0',
            'method' => 'twoParamsWithOpt',
            'params' => ['bar' => 'foo', 'foo' => 'baz', 'baz' => 'bar'],
            'id' => 123
        ]));
        $data = json_decode($res);
        $this->assertEquals('baz', $data->result->foo);
        $this->assertEquals('foo', $data->result->bar);
        $this->assertEquals('bar', $data->result->baz);

        $res = $server->respond(json_encode([
            'jsonrpc' => '2.0',
            'method' => 'twoParamsWithOpt',
            'params' => ['bar', 'foo'],
            'id' => 123
        ]));
        $data = json_decode($res);
        $this->assertEquals('bar', $data->result->foo);
        $this->assertEquals('foo', $data->result->bar);
        $this->assertEquals('baz', $data->result->baz);

        $res = $server->respond(json_encode([
            'jsonrpc' => '2.0',
            'method' => 'twoParamsWithOpt',
            'params' => ['baz', 'foo', 'bar'],
            'id' => 123
        ]));
        $data = json_decode($res);
        $this->assertEquals('baz', $data->result->foo);
        $this->assertEquals('foo', $data->result->bar);
        $this->assertEquals('bar', $data->result->baz);

        $res = $server->respond(json_encode([
            'jsonrpc' => '2.0',
            'method' => 'throwException',
            'id' => 123
        ]));
        $data = json_decode($res);
        $this->assertEquals('Foo', $data->error->message);
        $this->assertTrue($data->error->code === 0);

        $res = $server->respond(json_encode([
            'jsonrpc' => '2.0',
            'method' => 'throwExceptionWithData',
            'id' => 123
        ]));
        $data = json_decode($res, true);
        $this->assertEquals('FooBar', $data['error']['message']);
        $this->assertTrue($data['error']['code'] === 123);
        $this->assertEquals(['foo' => 'bar'], $data['error']['data']);

        $res = $server->respond(json_encode([
            'jsonrpc' => '2.0',
            'method' => 'throwJsonRpcException',
            'id' => 123
        ]));
        $data = json_decode($res, true);
        $this->assertEquals('FooBar', $data['error']['message']);
        $this->assertTrue($data['error']['code'] === 123);

        $res = $server->respond(json_encode([
            'jsonrpc' => '2.0',
            'method' => 'noParams'
        ]));
        $this->assertEquals('', $res);

        $res = $server->respond(json_encode([
            'jsonrpc' => '2.0',
            'method' => 'oneParam'
        ]));
        $this->assertEquals('', $res);

        $res = $server->respond(json_encode([
            'jsonrpc' => '2.0',
            'method' => 'oneParam',
            'params' => [1, 2]
        ]));
        $this->assertEquals('', $res);

        $res = $server->respond(json_encode([
            'jsonrpc' => '2.0',
            'method' => 'oneParam',
            'params' => ['bar' => 'foo']
        ]));
        $this->assertEquals('', $res);

        $res = $server->respond(json_encode([
            'jsonrpc' => '2.0',
            'method' => 'throwJsonRpcException'
        ]));
        $this->assertEquals('', $res);

        $res = $server->respond(json_encode([
            'jsonrpc' => '2.0',
            'method' => 'throwException'
        ]));
        $this->assertEquals('', $res);
    }
}
