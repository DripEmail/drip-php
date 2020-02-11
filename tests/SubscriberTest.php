<?php

namespace DripTests;

use GuzzleHttp\Psr7\Response;

require_once 'support\DripClientTestBase.php';

class SubscriberTest extends DripClientTestBase
{
    // #create_or_update_subscriber

    public function testCreateOrUpdateSubscriberBaseCase()
    {
        $this->client->append(new Response(200, [], '{"blah":"hello"}'));
        $response = $this->client->create_or_update_subscriber(['id' => '1212', 'blahparam' => 'blahvalue']);
        $this->assertTrue($response->is_success());
        $this->assertEquals('hello', $response->get_contents()['blah']);

        $this->assertRequest(
            'http://api.example.com/v9001/12345/subscribers',
            'POST',
            '{"subscribers":[{"id":"1212","blahparam":"blahvalue"}]}'
        );
    }

    // #create_or_update_subscribers

    public function testCreateOrUpdateSubscribersBaseCase()
    {
        $this->client->append(new Response(200, [], '{"blah":"hello"}'));
        $response = $this->client->create_or_update_subscribers(['batches' => [['subscribers' => [['blah1' => 'blah111'], ['blah2' => 'blah222']]]]]);
        $this->assertTrue($response->is_success());
        $this->assertEquals('hello', $response->get_contents()['blah']);
        $this->assertRequest(
            'http://api.example.com/v9001/12345/subscribers/batches',
            'POST',
            '{"batches":[{"subscribers":[{"blah1":"blah111"},{"blah2":"blah222"}]}]}'
        );
    }

    public function testBatchCreateOrUpdateSubscribersBaseCase()
    {
        $this->client->append(new Response(200, [], '{"blah":"hello"}'));
        $response = $this->client->batch_create_or_update_subscribers([['id' => '1212', 'blah1' => 'blah111'], ['id' => '1213', 'blah2' => 'blah222']]);
        $this->assertTrue($response->is_success());
        $this->assertEquals('hello', $response->get_contents()['blah']);

        $this->assertRequest(
            'http://api.example.com/v9001/12345/subscribers/batches',
            'POST',
            '{"batches":[{"subscribers":[{"id":"1212","blah1":"blah111"},{"id":"1213","blah2":"blah222"}]}]}'
        );
    }


    // #fetch_subscriber

    public function testFetchSubscriberById()
    {
        $this->client->append(new Response(200, [], '{"blah":"hello"}'));
        $response = $this->client->fetch_subscriber(['subscriber_id' => '1234']);

        $this->assertTrue($response->is_success());
        $this->assertEquals('hello', $response->get_contents()['blah']);
        $this->assertRequest('http://api.example.com/v9001/12345/subscribers/1234');
    }

    public function testFetchSubscriberByEmail()
    {
        $this->client->append(new Response(200, [], '{"blah":"hello"}'));
        $response = $this->client->fetch_subscriber(['email' => 'test@example.com']);

        $this->assertTrue($response->is_success());
        $this->assertEquals('hello', $response->get_contents()['blah']);
        $this->assertRequest('http://api.example.com/v9001/12345/subscribers/test%40example.com');
    }

    public function testFetchSubscriberWithNeitherEmailNorId()
    {
        $this->client->append(new Response(200, [], '{"blah":"hello"}'));
        $this->expectException(\Drip\Exception\InvalidArgumentException::class);
        $response = $this->client->fetch_subscriber([]);
    }

    // #fetch_subscribers

    public function testFetchSubscribers()
    {
        $this->client->append(new Response(200, [], '{"blah":"hello"}'));
        $response = $this->client->fetch_subscribers();

        $this->assertTrue($response->is_success());
        $this->assertEquals('hello', $response->get_contents()['blah']);
        $this->assertRequest('http://api.example.com/v9001/12345/subscribers');
    }

    // #subscribe_subscriber

    public function testSubscribeSubscriberBaseCase()
    {
        $this->client->append(new Response(200, [], '{"blah":"hello"}'));
        $response = $this->client->subscribe_subscriber(['campaign_id' => '1234', 'email' => 'test@example.com']);

        $this->assertTrue($response->is_success());
        $this->assertEquals('hello', $response->get_contents()['blah']);
        $this->assertRequest(
            'http://api.example.com/v9001/12345/campaigns/1234/subscribers',
            'POST',
            '{"subscribers":[{"email":"test@example.com","double_optin":true}]}'
        );
    }

    public function testSubscribeSubscriberWithDoubleOptin()
    {
        $this->client->append(new Response(200, [], '{"blah":"hello"}'));
        $response = $this->client->subscribe_subscriber(['campaign_id' => '1234', 'email' => 'test@example.com', 'double_optin' => false]);

        $this->assertTrue($response->is_success());
        $this->assertEquals('hello', $response->get_contents()['blah']);
        $this->assertRequest(
            'http://api.example.com/v9001/12345/campaigns/1234/subscribers',
            'POST',
            '{"subscribers":[{"email":"test@example.com","double_optin":false}]}'
        );
    }

    public function testSubscribeSubscriberWithoutCampaignId()
    {
        $this->client->append(new Response(200, [], '{"blah":"hello"}'));
        $this->expectException(\Drip\Exception\InvalidArgumentException::class);
        $this->client->subscribe_subscriber(['email' => 'test@example.com', 'double_optin' => false]);
    }

    public function testSubscribeSubscriberWithoutEmail()
    {
        $this->client->append(new Response(200, [], '{"blah":"hello"}'));
        $this->expectException(\Drip\Exception\InvalidArgumentException::class);
        $this->client->subscribe_subscriber(['campaign_id' => '1234', 'double_optin' => false]);
    }

    // #unsubscribe_subscriber

    public function testUnsubscribeSubscriberById()
    {
        $this->client->append(new Response(200, [], '{"blah":"hello"}'));
        $response = $this->client->unsubscribe_subscriber(['subscriber_id' => '1234']);

        $this->assertTrue($response->is_success());
        $this->assertEquals('hello', $response->get_contents()['blah']);
        $this->assertRequest('http://api.example.com/v9001/12345/subscribers/1234/unsubscribe', 'POST');
    }

    public function testUnsubscribeSubscriberByEmail()
    {
        $this->client->append(new Response(200, [], '{"blah":"hello"}'));
        $response = $this->client->unsubscribe_subscriber(['email' => 'test@example.com']);

        $this->assertTrue($response->is_success());
        $this->assertEquals('hello', $response->get_contents()['blah']);
        $this->assertRequest('http://api.example.com/v9001/12345/subscribers/test%40example.com/unsubscribe', 'POST');
    }

    public function testUnsubscribeSubscriberWithNeitherEmailNorId()
    {
        $this->client->append(new Response(200, [], '{"blah":"hello"}'));
        $this->expectException(\Drip\Exception\InvalidArgumentException::class);
        $response = $this->client->unsubscribe_subscriber([]);
    }

    // #tag_subscriber

    public function testTagSubscriberBaseCase()
    {
        $this->client->append(new Response(200, [], '{"blah":"hello"}'));
        $response = $this->client->tag_subscriber(['email' => 'test@example.com', 'tag' => 'blahblah']);

        $this->assertTrue($response->is_success());
        $this->assertEquals('hello', $response->get_contents()['blah']);
        $this->assertRequest(
            'http://api.example.com/v9001/12345/tags',
            'POST',
            '{"tags":[{"email":"test@example.com","tag":"blahblah"}]}'
        );
    }

    public function testTagSubscriberWithoutEmail()
    {
        $this->client->append(new Response(200, [], '{"blah":"hello"}'));
        $this->expectException(\Drip\Exception\InvalidArgumentException::class);
        $response = $this->client->tag_subscriber(['tag' => 'blahblah']);
    }

    public function testTagSubscriberWithoutTag()
    {
        $this->client->append(new Response(200, [], '{"blah":"hello"}'));
        $this->expectException(\Drip\Exception\InvalidArgumentException::class);
        $response = $this->client->tag_subscriber(['email' => 'test@example.com']);
    }

    // #untag_subscriber

    public function testUntagSubscriberBaseCase()
    {
        $this->client->append(new Response(200, [], '{"blah":"hello"}'));
        $response = $this->client->untag_subscriber(['email' => 'test@example.com', 'tag' => 'blahblah']);

        $this->assertTrue($response->is_success());
        $this->assertEquals('hello', $response->get_contents()['blah']);
        $this->assertRequest(
            'http://api.example.com/v9001/12345/tags',
            'DELETE',
            '{"tags":[{"email":"test@example.com","tag":"blahblah"}]}'
        );
    }

    public function testUntagSubscriberWithoutEmail()
    {
        $this->client->append(new Response(200, [], '{"blah":"hello"}'));
        $this->expectException(\Drip\Exception\InvalidArgumentException::class);
        $response = $this->client->untag_subscriber(['tag' => 'blahblah']);
    }

    public function testUntagSubscriberWithoutTag()
    {
        $this->client->append(new Response(200, [], '{"blah":"hello"}'));
        $this->expectException(\Drip\Exception\InvalidArgumentException::class);
        $response = $this->client->untag_subscriber(['email' => 'test@example.com']);
    }

    public function testCampaignSubscriptionsBaseCase()
    {
        $this->client->append(new Response(200, [], '{"blah":"hello"}'));
        $response = $this->client::campaign_subscriptions('9876');
        $this->assertRequest('http://api.example.com/v9001/12345/subscribers/9876/campaign_subscriptions');
    }
}
