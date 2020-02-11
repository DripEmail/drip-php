<?php

namespace DripTests;

use GuzzleHttp\Psr7\Response;

require_once 'support\DripClientTestBase.php';

class CampaignTest extends DripClientTestBase
{
    // #get_campaigns
    public function testGetCampaignsBaseCase()
    {
        $this->client->append(new Response(200, [], '{"blah":"hello"}'));
        $response = $this->client->get_campaigns([]);

        $this->assertTrue($response->is_success());
        $this->assertEquals('hello', $response->get_contents()['blah']);
        $this->assertRequest('http://api.example.com/v9001/12345/campaigns');
    }

    public function testGetCampaignsValidStatus()
    {
        $this->client->append(new Response(200, [], '{"blah":"hello"}'));
        $response = $this->client->get_campaigns(['status' => 'active']);

        $this->assertTrue($response->is_success());
        $this->assertEquals('hello', $response->get_contents()['blah']);
        $this->assertRequest('http://api.example.com/v9001/12345/campaigns?status=active');
    }

    public function testGetCampaignsInvalidStatus()
    {
        $this->client->append(new Response(200, [], '{"blah":"hello"}'));
        $this->expectException(\Drip\Exception\InvalidArgumentException::class);
        $this->client->get_campaigns(['status' => 'blah']);
    }

    public function testGetCampaignsArbitraryParam()
    {
        $this->client->append(new Response(200, [], '{"blah":"hello"}'));
        $response = $this->client->get_campaigns(['myparam' => 'blah']);

        $this->assertTrue($response->is_success());
        $this->assertEquals('hello', $response->get_contents()['blah']);
        $this->assertRequest('http://api.example.com/v9001/12345/campaigns?myparam=blah');
    }

    // #fetch_campaign

    public function testFetchCampaignBaseCase()
    {
        $this->client->append(new Response(200, [], '{"blah":"hello"}'));
        $response = $this->client->fetch_campaign(['campaign_id' => 13579]);

        $this->assertTrue($response->is_success());
        $this->assertEquals('hello', $response->get_contents()['blah']);
        $this->assertRequest('http://api.example.com/v9001/12345/campaigns/13579');
    }

    public function testFetchCampaignMissingId()
    {
        $this->client->append(new Response(200, [], '{"blah":"hello"}'));
        $this->expectException(\Drip\Exception\InvalidArgumentException::class);
        $this->client->fetch_campaign([]);
    }

    // #activate_campaign

    public function testActivate_campaignBaseCase()
    {
        $this->client->append(new Response(200, [], '{"blah":"hello"}'));
        $response = $this->client->activate_campaign(['campaign_id' => 13579]);

        $this->assertTrue($response->is_success());
        $this->assertRequest('http://api.example.com/v9001/12345/campaigns/13579/activate', 'POST');
    }

    public function testActivate_campaignMissingId()
    {
        $this->client->append(new Response(200, [], '{"blah":"hello"}'));
        $this->expectException(\Drip\Exception\InvalidArgumentException::class);
        $this->client->activate_campaign([]);
    }

    // #pause_campaign

    public function testPause_campaignBaseCase()
    {
        $this->client->append(new Response(204, []));
        $response = $this->client->pause_campaign(['campaign_id' => 13579]);

        $this->assertTrue($response->is_success());
        $this->assertRequest('http://api.example.com/v9001/12345/campaigns/13579/pause', 'POST');
    }

    public function testPause_campaignMissingId()
    {
        $this->client->append(new Response(200, [], '{"blah":"hello"}'));

        $this->expectException(\Drip\Exception\InvalidArgumentException::class);
        $this->client->pause_campaign([]);
    }

    // TODO: Write test cases for fetching campaign_subscribers

    public function testCampaign_subscribersBaseCase()
    {
        $mocked_requests = [];

    }
}
