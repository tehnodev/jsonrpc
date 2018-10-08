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
    }

    // function testApi() {
    //     $server = new Server(new Api());
    //     $res = $server->respond(new Request(1, 'oneParam'));
    //     $data = json_decode($res);
    //     $this->assertNull($data->result);
    // }
}
