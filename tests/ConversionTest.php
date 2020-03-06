<?php

namespace DripTests;

use DripTests\support\DripClientTestBase;
use GuzzleHttp\Psr7\Response;

class ConversionTest extends DripClientTestBase
{

    public function testGetConversionsBaseCase()
    {
        $this->client->append(new Response(200, [], '{"blah":"hello"}'));
        $response = $this->client->get_conversions();

        $this->assertTrue($response->is_success());
        $this->assertEquals('hello', $response->get_contents()['blah']);
        $this->assertRequest('http://api.example.com/v9001/12345/goals');
    }

    public function testFetchConversionBaseCase()
    {
        $this->client->append(new Response(200, [], '{"blah":"hello"}'));
        $response = $this->client->fetch_conversion(['conversion_id' => '6789']);

        $this->assertTrue($response->is_success());
        $this->assertEquals('hello', $response->get_contents()['blah']);
        $this->assertRequest('http://api.example.com/v9001/12345/goals/6789');
    }
}
