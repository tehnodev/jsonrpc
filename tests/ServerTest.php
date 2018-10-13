<?php

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

        $res = json_decode($server->respond(new Request(1, 'add')));
        $this->assertEquals(-32601, $res->error->code);
        $this->assertEquals('Method not found', $res->error->message);
        $this->assertEquals('add', $res->error->data);

        $res = json_decode($server->respond(new Request(1, 'oneParam')));
        $this->assertEquals(-32602, $res->error->code);
        $this->assertEquals('Invalid params', $res->error->message);
        $this->assertEquals('Params count', $res->error->data);

        $res = json_decode($server->respond(new Request(1, 'twoParams', ['foo'])));
        $this->assertEquals(-32602, $res->error->code);
        $this->assertEquals('Invalid params', $res->error->message);
        $this->assertEquals('Params count', $res->error->data);

        $res = json_decode($server->respond(new Request(1, 'oneParam', ['foo', 'bar'])));
        $this->assertEquals(-32602, $res->error->code);
        $this->assertEquals('Invalid params', $res->error->message);
        $this->assertEquals('Params count', $res->error->data);

        $res = json_decode($server->respond(new Request(1, 'twoParamsWithOpt', ['foo' =>'bar', 'baz' => 'foo'])));
        $this->assertEquals(-32602, $res->error->code);
        $this->assertEquals('Invalid params', $res->error->message);
    }

    function testApi() {
        $server = new Server(new Api());
        
        $res = $server->respond(new Request(123, 'noParams'));
        $data = json_decode($res);
        $this->assertNull($data->result);
        $this->assertEquals(123, $data->id);
        
        $res = $server->respond(new Request(123, 'oneParam', ['foo']));
        $data = json_decode($res);
        $this->assertEquals('foo', $data->result);
        $this->assertEquals(123, $data->id);

        $res = $server->respond(new Request(123, 'oneParam', ['foo' => 'bar']));
        $data = json_decode($res);
        $this->assertEquals('bar', $data->result);
        $this->assertEquals(123, $data->id);

        $res = $server->respond(new Request(123, 'optParam'));
        $data = json_decode($res);
        $this->assertEquals('foo', $data->result);
        $this->assertEquals(123, $data->id);

        $res = $server->respond(new Request(123, 'optParam', ['bar']));
        $data = json_decode($res);
        $this->assertEquals('bar', $data->result);
        $this->assertEquals(123, $data->id);

        $res = $server->respond(new Request(123, 'optParam', ['foo' => 'bar']));
        $data = json_decode($res);
        $this->assertEquals('bar', $data->result);
        $this->assertEquals(123, $data->id);

        $res = $server->respond(new Request(123, 'twoParams', ['bar', 'foo']));
        $data = json_decode($res);
        $this->assertEquals('bar', $data->result->foo);
        $this->assertEquals('foo', $data->result->bar);
        $this->assertEquals(123, $data->id);

        $res = $server->respond(new Request(123, 'twoParams', ['foo' => 'bar', 'bar' => 'foo']));
        $data = json_decode($res);
        $this->assertEquals('bar', $data->result->foo);
        $this->assertEquals('foo', $data->result->bar);
        $this->assertEquals(123, $data->id);

        $res = $server->respond(new Request(123, 'twoParamsWithOpt', ['bar' => 'foo', 'foo' => 'bar']));
        $data = json_decode($res);
        $this->assertEquals('bar', $data->result->foo);
        $this->assertEquals('foo', $data->result->bar);
        $this->assertEquals('baz', $data->result->baz);
        $this->assertEquals(123, $data->id);

        $res = $server->respond(new Request(123, 'twoParamsWithOpt', ['bar' => 'foo', 'foo' => 'baz', 'baz' => 'bar']));
        $data = json_decode($res);
        $this->assertEquals('baz', $data->result->foo);
        $this->assertEquals('foo', $data->result->bar);
        $this->assertEquals('bar', $data->result->baz);
        $this->assertEquals(123, $data->id);

        $res = $server->respond(new Request(123, 'twoParamsWithOpt', ['bar', 'foo']));
        $data = json_decode($res);
        $this->assertEquals('bar', $data->result->foo);
        $this->assertEquals('foo', $data->result->bar);
        $this->assertEquals('baz', $data->result->baz);
        $this->assertEquals(123, $data->id);

        $res = $server->respond(new Request(123, 'twoParamsWithOpt', ['baz', 'foo', 'bar']));
        $data = json_decode($res);
        $this->assertEquals('baz', $data->result->foo);
        $this->assertEquals('foo', $data->result->bar);
        $this->assertEquals('bar', $data->result->baz);
        $this->assertEquals(123, $data->id);

        $res = $server->respond(new Request(123, 'throwException'));
        $data = json_decode($res);
        $this->assertEquals('Foo', $data->error->message);
        $this->assertEquals(0, $data->error->code);

        $res = $server->respond(new Request(123, 'throwExceptionWithData'));
        $data = json_decode($res, true);
        $this->assertEquals('FooBar', $data['error']['message']);
        $this->assertEquals(123, $data['error']['code']);
        $this->assertEquals(['foo' => 'bar'], $data['error']['data']);
    }
}
