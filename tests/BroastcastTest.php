<?php

namespace DripTests;

use DripTests\support\DripClientTestBase;
use GuzzleHttp\Psr7\Response;

class BroastcastTest extends DripClientTestBase
{
    public function testGetBroastcastsBaseCase()
    {
        $this->client->append(new Response(200, [], '{"blah":"hello"}'));
        $response = $this->client->get_broadcasts();

        $this->assertTrue($response->is_success());
        $this->assertEquals('hello', $response->get_contents()['blah']);
        $this->assertRequest('http://api.example.com/v9001/12345/broadcasts');
    }

    public function testGetBroastcastsWithParams()
    {
        $this->client->append(new Response(200, [], '{"blah":"hello"}'));
        $response = $this->client->get_broadcasts(['status' => 'all', 'direction' => 'desc', 'sort' => 'created_at']);

        $this->assertTrue($response->is_success());
        $this->assertEquals('hello', $response->get_contents()['blah']);
        $this->assertRequest('http://api.example.com/v9001/12345/broadcasts?status=all&direction=desc&sort=created_at');
    }

    public function testFetchBroastcastsBaseCase()
    {
        $this->client->append(new Response(200, [], '{"blah":"hello"}'));
        $response = $this->client->fetch_broadcast(['broadcast_id' => '1234']);

        $this->assertTrue($response->is_success());
        $this->assertEquals('hello', $response->get_contents()['blah']);
        $this->assertRequest('http://api.example.com/v9001/12345/broadcasts/1234');
    }

}
