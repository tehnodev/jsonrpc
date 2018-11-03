<?php

use PHPUnit\Framework\TestCase;
use Tehnodev\JsonRpc\Server;
use Tehnodev\JsonRpc\Test\Api;

// https://www.jsonrpc.org/specification#examples
class ExamplesTest extends TestCase {
    function testPositionalParams() {
        $server = new Server(new Api());

        $res = $server->respond(json_encode([
            'jsonrpc' => '2.0',
            'method' => 'subtract',
            'params' => [42, 23],
            'id' => 1
        ]));

        $res = json_decode($res);
        $this->assertEquals('2.0', $res->jsonrpc);
        $this->assertTrue($res->result === 19);
        $this->assertTrue($res->id === 1);

        $res = $server->respond(json_encode([
            'jsonrpc' => '2.0',
            'method' => 'subtract',
            'params' => [23, 42],
            'id' => 2
        ]));

        $res = json_decode($res);
        $this->assertEquals('2.0', $res->jsonrpc);
        $this->assertTrue($res->result === -19);
        $this->assertTrue($res->id === 2);
    }

    function testNamedParams() {
        $server = new Server(new Api());

        $res = $server->respond(json_encode([
            'jsonrpc' => '2.0',
            'method' => 'subtract',
            'params' => ['subtrahend' => 23, 'minuend' => 42],
            'id' => 3
        ]));

        $res = json_decode($res);
        $this->assertEquals('2.0', $res->jsonrpc);
        $this->assertTrue($res->result === 19);
        $this->assertTrue($res->id === 3);

        $res = $server->respond(json_encode([
            'jsonrpc' => '2.0',
            'method' => 'subtract',
            'params' => ['minuend' => 42, 'subtrahend' => 23],
            'id' => 4
        ]));

        $res = json_decode($res);
        $this->assertEquals('2.0', $res->jsonrpc);
        $this->assertTrue($res->result === 19);
        $this->assertTrue($res->id === 4);
    }

    function testNotification() {
        $server = new Server(new Api());

        $res = $server->respond(json_encode([
            'jsonrpc' => '2.0',
            'method' => 'update',
            'params' => [1, 2, 3, 4, 5]
        ]));

        $this->assertEquals('', $res);

        $res = $server->respond(json_encode([
            'jsonrpc' => '2.0',
            'method' => 'foobar'
        ]));

        $this->assertEquals('', $res);
    }

    function testNonExistentMethod() {
        $server = new Server(new Api());

        $res = $server->respond(json_encode([
            'jsonrpc' => '2.0',
            'method' => 'foobaz',
            'id' => '1'
        ]));

        $res = json_decode($res);
        $this->assertEquals('2.0', $res->jsonrpc);
        $this->assertTrue($res->error->code === -32601);
        $this->assertEquals('Method not found', $res->error->message);
        $this->assertTrue($res->id === '1');
    }

    function testInvalidJson() {
        $server = new Server(new Api());

        $res = $server->respond('{"jsonrpc": "2.0", "method": "foobar, "params": "bar", "baz]');

        $res = json_decode($res);
        $this->assertEquals('2.0', $res->jsonrpc);
        $this->assertTrue($res->error->code === -32700);
        $this->assertEquals('Parse error', $res->error->message);
        $this->assertTrue($res->id === null);
    }

    function testInvalidRequestObject() {
        $server = new Server(new Api());

        $res = $server->respond(json_encode([
            'jsonrpc' => '2.0',
            'method' => 1,
            'params' => 'bar'
        ]));

        $res = json_decode($res);
        $this->assertEquals('2.0', $res->jsonrpc);
        $this->assertTrue($res->error->code === -32600);
        $this->assertEquals('Invalid Request', $res->error->message);
        $this->assertTrue($res->id === null);
    }

    function testBatchInvalidJson() {
        $server = new Server(new Api());

        $res = $server->respond('
            {"jsonrpc": "2.0", "method": "sum", "params": [1,2,4], "id": "1"},
            {"jsonrpc": "2.0", "method"
        ');

        $res = json_decode($res);
        $this->assertEquals('2.0', $res->jsonrpc);
        $this->assertTrue($res->error->code === -32700);
        $this->assertEquals('Parse error', $res->error->message);
        $this->assertTrue($res->id === null);
    }

    function testEmptyArray() {
        $server = new Server(new Api());

        $res = $server->respond('[]');

        $res = json_decode($res);
        $this->assertEquals('2.0', $res->jsonrpc);
        $this->assertTrue($res->error->code === -32600);
        $this->assertEquals('Invalid Request', $res->error->message);
        $this->assertTrue($res->id === null);
    }

    function testBatchInvalidNotEmpty() {
        $server = new Server(new Api());

        $res = $server->respond('[1]');

        $res = json_decode($res);
        $this->assertCount(1, $res);
        $this->assertEquals('2.0', $res[0]->jsonrpc);
        $this->assertTrue($res[0]->error->code === -32600);
        $this->assertEquals('Invalid Request', $res[0]->error->message);
        $this->assertTrue($res[0]->id === null);
    }

    function testBatchInvalid() {
        $server = new Server(new Api());

        $res = $server->respond('[1, 2, 3]');

        $res = json_decode($res);
        $this->assertCount(3, $res);

        $this->assertEquals('2.0', $res[0]->jsonrpc);
        $this->assertTrue($res[0]->error->code === -32600);
        $this->assertEquals('Invalid Request', $res[0]->error->message);
        $this->assertTrue($res[0]->id === null);

        $this->assertEquals('2.0', $res[1]->jsonrpc);
        $this->assertTrue($res[1]->error->code === -32600);
        $this->assertEquals('Invalid Request', $res[1]->error->message);
        $this->assertTrue($res[1]->id === null);

        $this->assertEquals('2.0', $res[2]->jsonrpc);
        $this->assertTrue($res[2]->error->code === -32600);
        $this->assertEquals('Invalid Request', $res[2]->error->message);
        $this->assertTrue($res[2]->id === null);
    }

    function testBatch() {
        $server = new Server(new Api());

        $res = $server->respond(json_encode([
            ['jsonrpc' => '2.0', 'method' => 'sum', 'params' => [1, 2, 4], 'id' => '1'],
            ['jsonrpc' => '2.0', 'method' => 'notify_hello', 'params' => [7]],
            ['jsonrpc' => '2.0', 'method' => 'subtract', 'params' => [42, 23], 'id' => '2'],
            ['foo' => 'boo'],
            ['jsonrpc' => '2.0', 'method' => 'foo.get', 'params' => ['name' => 'myself'], 'id' => '5'],
            ['jsonrpc' => '2.0', 'method' => 'get_data', 'id' => '9']
        ]));

        $res = json_decode($res);
        $this->assertCount(5, $res);

        $this->assertEquals('2.0', $res[0]->jsonrpc);
        $this->assertTrue($res[0]->result === 7);
        $this->assertTrue($res[0]->id === '1');

        $this->assertEquals('2.0', $res[1]->jsonrpc);
        $this->assertTrue($res[1]->result === 19);
        $this->assertTrue($res[1]->id === '2');

        $this->assertEquals('2.0', $res[2]->jsonrpc);
        $this->assertTrue($res[2]->error->code === -32600);
        $this->assertEquals('Invalid Request', $res[2]->error->message);
        $this->assertTrue($res[2]->id === null);

        $this->assertEquals('2.0', $res[3]->jsonrpc);
        $this->assertTrue($res[3]->error->code === -32601);
        $this->assertEquals('Method not found', $res[3]->error->message);
        $this->assertTrue($res[3]->id === '5');

        $this->assertEquals('2.0', $res[4]->jsonrpc);
        $this->assertEquals(['hello', 5], $res[4]->result);
        $this->assertTrue($res[4]->id === '9');
    }

    function testBatchAllNotifications() {
        $server = new Server(new Api());

        $res = $server->respond(json_encode([
            ['jsonrpc' => '2.0', 'method' => 'notify_sum', 'params' => [1, 2, 4]],
            ['jsonrpc' => '2.0', 'method' => 'notify_hello', 'params' => [7]]
        ]));

        $this->assertTrue($res === '');
    }
}