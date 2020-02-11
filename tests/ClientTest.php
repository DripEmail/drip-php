<?php

namespace DripTests;

use GuzzleHttp\Psr7\Response;

require_once 'support\DripClientTestBase.php';

final class ClientTest extends DripClientTestBase
{
    public function testInitializedWithApiToken()
    {
        $client = new \Drip\Client("abc123", "1234");
        $this->assertInstanceOf(\Drip\Client::class, $client);
    }

    public function testInvalidApiToken()
    {
        $this->expectException(\Drip\Exception\InvalidApiTokenException::class);
        new \Drip\Client("", "1234");
    }

    public function testInvalidAccountId()
    {
        $this->expectException(\Drip\Exception\InvalidAccountIdException::class);
        new \Drip\Client("abc123", "");
    }

    public function testStaticClientEnvSetup()
    {
        putenv('DRIP_API_TOKEN=abc123');
        putenv('DRIP_ACCOUNT_ID=1234');
        \Drip\Client::init_client();
        $this->assertTrue(\Drip\Client::is_initialized());
    }


    public function testErrorResponseReturned()
    {
        $this->client->append(new Response(401, [], '{"error":"hello"}'));
        $this->client->append(new Response(502, [], 'timeout'));
        $response401 = $this->client->fetch_campaign(['campaign_id' => 1]);
        $this->assertFalse($response401->is_success());
        $response502 = $this->client->fetch_campaign(['campaign_id' => 1]);
        $this->assertFalse($response502->is_success());
    }

}
