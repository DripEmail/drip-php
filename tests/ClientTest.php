<?php

namespace DripTests;

use PHPUnit\Framework\TestCase;

use GuzzleHttp\Psr7\Response;
use GuzzleHttp\Psr7\Request;

require_once 'GuzzleHelpers.php';

final class ClientTest extends TestCase
{
    public function testDeprecatedInitializedWithApiKey()
    {
        $client = new \Drip\Client("abc123", "1234");
        $this->assertInstanceOf(\Drip\Client::class, $client);
    }

    public function testInitializedWithAccessToken()
    {
        $client = new \Drip\Client(["account_id" => "1234", "access_token" => "abc123"]);
        $this->assertInstanceOf(\Drip\Client::class, $client);
    }

    public function testInitializedWithApiKey()
    {
        $client = new \Drip\Client(["account_id" => "1234", "api_key" => "abc123"]);
        $this->assertInstanceOf(\Drip\Client::class, $client);
    }

    public function testDeprecatedInvalidApiKey()
    {
        $this->expectException(\Drip\Exception\InvalidApiKeyException::class);
        new \Drip\Client("", "1234");
    }

    public function testInvalidApiKey()
    {
        $this->expectException(\Drip\Exception\InvalidApiKeyException::class);
        new \Drip\Client(["account_id" => "1234", "api_key" => ""]);
    }

    public function testInvalidAccessToken()
    {
        $this->expectException(\Drip\Exception\InvalidAccessTokenException::class);
        new \Drip\Client(["account_id" => "1234", "access_token" => ""]);
    }

    public function testMissingCredentials()
    {
        $this->expectException(\Drip\Exception\InvalidArgumentException::class);
        new \Drip\Client(["account_id" => "1234"]);
    }

    public function testDeprecatedInvalidAccountId()
    {
        $this->expectException(\Drip\Exception\InvalidAccountIdException::class);
        new \Drip\Client("abc123", "");
    }

    public function testInvalidAccountId()
    {
        $this->expectException(\Drip\Exception\InvalidAccountIdException::class);
        new \Drip\Client(["account_id" => "", "api_key" => "abc123"]);
    }

    public function testErrorResponseReturned()
    {
        $mocked_requests = [];
        $client = GuzzleHelpers::mocked_client($mocked_requests, [
            // default client request option "http_errors" will throw
            // GuzzleHttp\Exception\ClientException but we set it false so
            // no exception is thrown.
            new Response(401, [], '{"error":"hello"}'),
            new Response(502, [], 'timeout'),
        ]);
        $response401 = $client->fetch_campaign(['campaign_id' => 1]);
        $this->assertFalse($response401->is_success());
        $response502 = $client->fetch_campaign(['campaign_id' => 1]);
        $this->assertFalse($response502->is_success());
    }

    ////////////////////////// C A M P A I G N S //////////////////////////

    // #get_campaigns

    public function testGetCampaignsBaseCase()
    {
        $mocked_requests = [];
        $client = GuzzleHelpers::mocked_client($mocked_requests, [
            new Response(200, [], '{"blah":"hello"}'),
        ]);
        $response = $client->get_campaigns([]);
        $this->assertTrue($response->is_success());
        $this->assertEquals('hello', $response->get_contents()['blah']);

        $this->assertCount(1, $mocked_requests);
        $uri = $mocked_requests[0]['request']->getUri();
        $this->assertEquals('http://api.example.com/v9001/12345/campaigns', $uri);
    }

    public function testGetCampaignsValidStatus()
    {
        $mocked_requests = [];
        $client = GuzzleHelpers::mocked_client($mocked_requests, [
            new Response(200, [], '{"blah":"hello"}'),
        ]);
        $response = $client->get_campaigns(['status' => 'active']);
        $this->assertTrue($response->is_success());
        $this->assertEquals('hello', $response->get_contents()['blah']);

        $this->assertCount(1, $mocked_requests);
        $uri = $mocked_requests[0]['request']->getUri();
        $this->assertEquals('http://api.example.com/v9001/12345/campaigns?status=active', $uri);
    }

    public function testGetCampaignsInvalidStatus()
    {
        $mocked_requests = [];
        $client = GuzzleHelpers::mocked_client($mocked_requests, [
            new Response(200, [], '{"blah":"hello"}'),
        ]);
        $this->expectException(\Drip\Exception\InvalidArgumentException::class);
        $client->get_campaigns(['status' => 'blah']);
    }

    public function testGetCampaignsArbitraryParam()
    {
        $mocked_requests = [];
        $client = GuzzleHelpers::mocked_client($mocked_requests, [
            new Response(200, [], '{"blah":"hello"}'),
        ]);
        $response = $client->get_campaigns(['myparam' => 'blah']);
        $this->assertTrue($response->is_success());
        $this->assertEquals('hello', $response->get_contents()['blah']);

        $this->assertCount(1, $mocked_requests);
        $uri = $mocked_requests[0]['request']->getUri();
        $this->assertEquals('http://api.example.com/v9001/12345/campaigns?myparam=blah', $uri);
    }

    // #fetch_campaign

    public function testFetchCampaignBaseCase()
    {
        $mocked_requests = [];
        $client = GuzzleHelpers::mocked_client($mocked_requests, [
            new Response(200, [], '{"blah":"hello"}'),
        ]);
        $response = $client->fetch_campaign(['campaign_id' => 13579]);
        $this->assertTrue($response->is_success());
        $this->assertEquals('hello', $response->get_contents()['blah']);

        $this->assertCount(1, $mocked_requests);
        $uri = $mocked_requests[0]['request']->getUri();
        $this->assertEquals('http://api.example.com/v9001/12345/campaigns/13579', $uri);
    }

    public function testFetchCampaignMissingId()
    {
        $mocked_requests = [];
        $client = GuzzleHelpers::mocked_client($mocked_requests, [
            new Response(200, [], '{"blah":"hello"}'),
        ]);
        $this->expectException(\Drip\Exception\InvalidArgumentException::class);
        $client->fetch_campaign([]);
    }

    ////////////////////////// A C C O U N T S //////////////////////////

    // #get_accounts

    public function testGetAccountsBaseCase()
    {
        $mocked_requests = [];
        $client = GuzzleHelpers::mocked_client($mocked_requests, [
            new Response(200, [], '{"blah":"hello"}'),
        ]);
        $response = $client->get_accounts();
        $this->assertTrue($response->is_success());
        $this->assertEquals('hello', $response->get_contents()['blah']);

        $this->assertCount(1, $mocked_requests);
        $uri = $mocked_requests[0]['request']->getUri();
        $this->assertEquals('http://api.example.com/v9001/accounts', $uri);
    }

    ////////////////////////// S U B S C R I B E R S //////////////////////////

    // #create_or_update_subscriber

    public function testCreateOrUpdateSubscriberBaseCase()
    {
        $mocked_requests = [];
        $client = GuzzleHelpers::mocked_client($mocked_requests, [
            new Response(200, [], '{"blah":"hello"}'),
        ]);
        $response = $client->create_or_update_subscriber(['blahparam' => 'blahvalue']);
        $this->assertTrue($response->is_success());
        $this->assertEquals('hello', $response->get_contents()['blah']);

        $this->assertCount(1, $mocked_requests);
        $req = $mocked_requests[0]['request'];
        $this->assertEquals('http://api.example.com/v9001/12345/subscribers', $req->getUri());
        $this->assertEquals('POST', $req->getMethod());
        $this->assertEquals('{"subscribers":[{"blahparam":"blahvalue"}]}', (string) $req->getBody());
    }

    // #create_or_update_subscribers

    public function testCreateOrUpdateSubscribersBaseCase()
    {
        $mocked_requests = [];
        $client = GuzzleHelpers::mocked_client($mocked_requests, [
            new Response(200, [], '{"blah":"hello"}'),
        ]);
        $response = $client->create_or_update_subscribers(['batches' => [['subscribers' => [['blah1' => 'blah111'],['blah2' => 'blah222']]]]]);
        $this->assertTrue($response->is_success());
        $this->assertEquals('hello', $response->get_contents()['blah']);

        $this->assertCount(1, $mocked_requests);
        $req = $mocked_requests[0]['request'];
        $this->assertEquals('http://api.example.com/v9001/12345/subscribers/batches', $req->getUri());
        $this->assertEquals('POST', $req->getMethod());
        $this->assertEquals('{"batches":[{"subscribers":[{"blah1":"blah111"},{"blah2":"blah222"}]}]}', (string) $req->getBody());
    }

    // #fetch_subscriber

    public function testFetchSubscriberById()
    {
        $mocked_requests = [];
        $client = GuzzleHelpers::mocked_client($mocked_requests, [
            new Response(200, [], '{"blah":"hello"}'),
        ]);
        $response = $client->fetch_subscriber(['subscriber_id' => '1234']);
        $this->assertTrue($response->is_success());
        $this->assertEquals('hello', $response->get_contents()['blah']);

        $this->assertCount(1, $mocked_requests);
        $req = $mocked_requests[0]['request'];
        $this->assertEquals('http://api.example.com/v9001/12345/subscribers/1234', $req->getUri());
        $this->assertEquals('GET', $req->getMethod());
    }

    public function testFetchSubscriberByEmail()
    {
        $mocked_requests = [];
        $client = GuzzleHelpers::mocked_client($mocked_requests, [
            new Response(200, [], '{"blah":"hello"}'),
        ]);
        $response = $client->fetch_subscriber(['email' => 'test@example.com']);
        $this->assertTrue($response->is_success());
        $this->assertEquals('hello', $response->get_contents()['blah']);

        $this->assertCount(1, $mocked_requests);
        $req = $mocked_requests[0]['request'];
        $this->assertEquals('http://api.example.com/v9001/12345/subscribers/test%40example.com', $req->getUri());
        $this->assertEquals('GET', $req->getMethod());
    }

    public function testFetchSubscriberWithNeitherEmailNorId()
    {
        $mocked_requests = [];
        $client = GuzzleHelpers::mocked_client($mocked_requests, [
            new Response(200, [], '{"blah":"hello"}'),
        ]);
        $this->expectException(\Drip\Exception\InvalidArgumentException::class);
        $response = $client->fetch_subscriber([]);
    }

    // #fetch_subscriber_campaigns

    public function testFetchSubscriberCampaignsById()
    {
        $mocked_requests = [];
        $client = GuzzleHelpers::mocked_client($mocked_requests, [
            new Response(200, [], '{"blah":"hello"}'),
        ]);
        $response = $client->fetch_subscriber_campaigns(['subscriber_id' => '1234']);
        $this->assertTrue($response->is_success());
        $this->assertEquals('hello', $response->get_contents()['blah']);

        $this->assertCount(1, $mocked_requests);
        $req = $mocked_requests[0]['request'];
        $this->assertEquals('http://api.example.com/v9001/12345/subscribers/1234/campaign_subscriptions', $req->getUri());
        $this->assertEquals('GET', $req->getMethod());
    }

    public function testFetchSubscriberCampaignsByEmail()
    {
        $mocked_requests = [];
        $client = GuzzleHelpers::mocked_client($mocked_requests, [
            new Response(200, [], '{"blah":"hello"}'),
        ]);
        $response = $client->fetch_subscriber_campaigns(['email' => 'test@example.com']);
        $this->assertTrue($response->is_success());
        $this->assertEquals('hello', $response->get_contents()['blah']);

        $this->assertCount(1, $mocked_requests);
        $req = $mocked_requests[0]['request'];
        $this->assertEquals('http://api.example.com/v9001/12345/subscribers/test%40example.com/campaign_subscriptions', $req->getUri());
        $this->assertEquals('GET', $req->getMethod());
    }

    public function testFetchSubscriberCampaignsWithNeitherEmailNorId()
    {
        $mocked_requests = [];
        $client = GuzzleHelpers::mocked_client($mocked_requests, [
            new Response(200, [], '{"blah":"hello"}'),
        ]);
        $this->expectException(\Drip\Exception\InvalidArgumentException::class);
        $response = $client->fetch_subscriber_campaigns([]);
    }

    // #fetch_subscribers

    public function testFetchSubscribers()
    {
        $mocked_requests = [];
        $client = GuzzleHelpers::mocked_client($mocked_requests, [
            new Response(200, [], '{"blah":"hello"}'),
        ]);
        $response = $client->fetch_subscribers();
        $this->assertTrue($response->is_success());
        $this->assertEquals('hello', $response->get_contents()['blah']);

        $this->assertCount(1, $mocked_requests);
        $req = $mocked_requests[0]['request'];
        $this->assertEquals('http://api.example.com/v9001/12345/subscribers', $req->getUri());
        $this->assertEquals('GET', $req->getMethod());
    }

    // #subscribe_subscriber

    public function testSubscribeSubscriberBaseCase()
    {
        $mocked_requests = [];
        $client = GuzzleHelpers::mocked_client($mocked_requests, [
            new Response(200, [], '{"blah":"hello"}'),
        ]);
        $response = $client->subscribe_subscriber(['campaign_id' => '1234', 'email' => 'test@example.com']);
        $this->assertTrue($response->is_success());
        $this->assertEquals('hello', $response->get_contents()['blah']);

        $this->assertCount(1, $mocked_requests);
        $req = $mocked_requests[0]['request'];
        $this->assertEquals('http://api.example.com/v9001/12345/campaigns/1234/subscribers', $req->getUri());
        $this->assertEquals('POST', $req->getMethod());
        $this->assertEquals('{"subscribers":[{"email":"test@example.com","double_optin":true}]}', (string) $req->getBody());
    }

    public function testSubscribeSubscriberWithDoubleOptin()
    {
        $mocked_requests = [];
        $client = GuzzleHelpers::mocked_client($mocked_requests, [
            new Response(200, [], '{"blah":"hello"}'),
        ]);
        $response = $client->subscribe_subscriber(['campaign_id' => '1234', 'email' => 'test@example.com', 'double_optin' => false]);
        $this->assertTrue($response->is_success());
        $this->assertEquals('hello', $response->get_contents()['blah']);

        $this->assertCount(1, $mocked_requests);
        $req = $mocked_requests[0]['request'];
        $this->assertEquals('http://api.example.com/v9001/12345/campaigns/1234/subscribers', $req->getUri());
        $this->assertEquals('POST', $req->getMethod());
        $this->assertEquals('{"subscribers":[{"email":"test@example.com","double_optin":false}]}', (string) $req->getBody());
    }

    public function testSubscribeSubscriberWithoutCampaignId()
    {
        $mocked_requests = [];
        $client = GuzzleHelpers::mocked_client($mocked_requests, [
            new Response(200, [], '{"blah":"hello"}'),
        ]);
        $this->expectException(\Drip\Exception\InvalidArgumentException::class);
        $client->subscribe_subscriber(['email' => 'test@example.com', 'double_optin' => false]);
    }

    public function testSubscribeSubscriberWithoutEmail()
    {
        $mocked_requests = [];
        $client = GuzzleHelpers::mocked_client($mocked_requests, [
            new Response(200, [], '{"blah":"hello"}'),
        ]);
        $this->expectException(\Drip\Exception\InvalidArgumentException::class);
        $client->subscribe_subscriber(['campaign_id' => '1234', 'double_optin' => false]);
    }

    // #unsubscribe_subscriber

    public function testUnsubscribeSubscriberById()
    {
        $mocked_requests = [];
        $client = GuzzleHelpers::mocked_client($mocked_requests, [
            new Response(200, [], '{"blah":"hello"}'),
        ]);
        $response = $client->unsubscribe_subscriber(['subscriber_id' => '1234']);
        $this->assertTrue($response->is_success());
        $this->assertEquals('hello', $response->get_contents()['blah']);

        $this->assertCount(1, $mocked_requests);
        $req = $mocked_requests[0]['request'];
        $this->assertEquals('http://api.example.com/v9001/12345/subscribers/1234/unsubscribe', $req->getUri());
        $this->assertEquals('POST', $req->getMethod());
    }

    public function testUnsubscribeSubscriberByEmail()
    {
        $mocked_requests = [];
        $client = GuzzleHelpers::mocked_client($mocked_requests, [
            new Response(200, [], '{"blah":"hello"}'),
        ]);
        $response = $client->unsubscribe_subscriber(['email' => 'test@example.com']);
        $this->assertTrue($response->is_success());
        $this->assertEquals('hello', $response->get_contents()['blah']);

        $this->assertCount(1, $mocked_requests);
        $req = $mocked_requests[0]['request'];
        $this->assertEquals('http://api.example.com/v9001/12345/subscribers/test%40example.com/unsubscribe', $req->getUri());
        $this->assertEquals('POST', $req->getMethod());
    }

    public function testUnsubscribeSubscriberWithNeitherEmailNorId()
    {
        $mocked_requests = [];
        $client = GuzzleHelpers::mocked_client($mocked_requests, [
            new Response(200, [], '{"blah":"hello"}'),
        ]);
        $this->expectException(\Drip\Exception\InvalidArgumentException::class);
        $response = $client->unsubscribe_subscriber([]);
    }

    // #tag_subscriber

    public function testTagSubscriberBaseCase()
    {
        $mocked_requests = [];
        $client = GuzzleHelpers::mocked_client($mocked_requests, [
            new Response(200, [], '{"blah":"hello"}'),
        ]);
        $response = $client->tag_subscriber(['email' => 'test@example.com', 'tag' => 'blahblah']);
        $this->assertTrue($response->is_success());
        $this->assertEquals('hello', $response->get_contents()['blah']);

        $this->assertCount(1, $mocked_requests);
        $req = $mocked_requests[0]['request'];
        $this->assertEquals('http://api.example.com/v9001/12345/tags', $req->getUri());
        $this->assertEquals('POST', $req->getMethod());
        $this->assertEquals('{"tags":[{"email":"test@example.com","tag":"blahblah"}]}', (string) $req->getBody());
    }

    public function testTagSubscriberWithoutEmail()
    {
        $mocked_requests = [];
        $client = GuzzleHelpers::mocked_client($mocked_requests, [
            new Response(200, [], '{"blah":"hello"}'),
        ]);
        $this->expectException(\Drip\Exception\InvalidArgumentException::class);
        $response = $client->tag_subscriber(['tag' => 'blahblah']);
    }

    public function testTagSubscriberWithoutTag()
    {
        $mocked_requests = [];
        $client = GuzzleHelpers::mocked_client($mocked_requests, [
            new Response(200, [], '{"blah":"hello"}'),
        ]);
        $this->expectException(\Drip\Exception\InvalidArgumentException::class);
        $response = $client->tag_subscriber(['email' => 'test@example.com']);
    }

    // #untag_subscriber

    public function testUntagSubscriberBaseCase()
    {
        $mocked_requests = [];
        $client = GuzzleHelpers::mocked_client($mocked_requests, [
            new Response(200, [], '{"blah":"hello"}'),
        ]);
        $response = $client->untag_subscriber(['email' => 'test@example.com', 'tag' => 'blahblah']);
        $this->assertTrue($response->is_success());
        $this->assertEquals('hello', $response->get_contents()['blah']);

        $this->assertCount(1, $mocked_requests);
        $req = $mocked_requests[0]['request'];
        $this->assertEquals('http://api.example.com/v9001/12345/tags', $req->getUri());
        $this->assertEquals('DELETE', $req->getMethod());
        $this->assertEquals('{"tags":[{"email":"test@example.com","tag":"blahblah"}]}', (string) $req->getBody());
    }

    public function testUntagSubscriberWithoutEmail()
    {
        $mocked_requests = [];
        $client = GuzzleHelpers::mocked_client($mocked_requests, [
            new Response(200, [], '{"blah":"hello"}'),
        ]);
        $this->expectException(\Drip\Exception\InvalidArgumentException::class);
        $response = $client->untag_subscriber(['tag' => 'blahblah']);
    }

    public function testUntagSubscriberWithoutTag()
    {
        $mocked_requests = [];
        $client = GuzzleHelpers::mocked_client($mocked_requests, [
            new Response(200, [], '{"blah":"hello"}'),
        ]);
        $this->expectException(\Drip\Exception\InvalidArgumentException::class);
        $response = $client->untag_subscriber(['email' => 'test@example.com']);
    }

    ////////////////////////// E V E N T S //////////////////////////

    // #record_event

    public function testRecordEventBaseCase()
    {
        $mocked_requests = [];
        $client = GuzzleHelpers::mocked_client($mocked_requests, [
            new Response(200, [], '{"blah":"hello"}'),
        ]);
        $response = $client->record_event(['action' => 'blahaction']);
        $this->assertTrue($response->is_success());
        $this->assertEquals('hello', $response->get_contents()['blah']);

        $this->assertCount(1, $mocked_requests);
        $req = $mocked_requests[0]['request'];
        $this->assertEquals('http://api.example.com/v9001/12345/events', $req->getUri());
        $this->assertEquals('POST', $req->getMethod());
        $this->assertEquals('{"events":[{"action":"blahaction"}]}', (string) $req->getBody());
    }

    public function testRecordEventMissingAction()
    {
        $mocked_requests = [];
        $client = GuzzleHelpers::mocked_client($mocked_requests, [
            new Response(200, [], '{"blah":"hello"}'),
        ]);
        $this->expectException(\Drip\Exception\InvalidArgumentException::class);
        $client->record_event([]);
    }
}
