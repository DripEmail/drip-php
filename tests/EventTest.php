<?php

namespace DripTests;

use DripTests\support\DripClientTestBase;
use GuzzleHttp\Psr7\Response;

class EventTest extends DripClientTestBase
{
    public function testgetEventActionsBaseCase()
    {
        $this->client->append(new Response(200, [], '{"blah":"hello"}'));
        $response = $this->client->get_event_actions(['per_page' => 1000]);

        $this->assertTrue($response->is_success());
        $this->assertEquals('hello', $response->get_contents()['blah']);
        $this->assertRequest('http://api.example.com/v9001/12345/event_actions?per_page=1000');
    }

    public function testRecordEventBaseCase()
    {
        $this->client->append(new Response(200, [], '{"blah":"hello"}'));
        $response = $this->client->record_event([
            'action' => 'blahaction',
            'email' => 'testemail@test.com',
            'properties' => ['affiliate_code' => 'XYZ'],
            'occurred_at' => '2014-03-22T03:00:00Z'
        ]);
        $this->assertTrue($response->is_success());
        $this->assertEquals('hello', $response->get_contents()['blah']);
        $this->assertRequest(
            'http://api.example.com/v9001/12345/events',
            'POST',
            '{"events": [
            {"occurred_at":"2014-03-22T03:00:00Z", 
            "properties":{"affiliate_code":"XYZ"}, 
            "email": "testemail@test.com",
            "action": "blahaction"}]
            }'
        );
    }

    public function testRecordEventMissingAction()
    {
        $this->client->append(new Response(200, [], '{"blah":"hello"}'));
        $this->expectException(\Drip\Exception\InvalidArgumentException::class);
        $this->client->record_event([]);
    }
}
