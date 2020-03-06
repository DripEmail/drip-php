<?php

namespace DripTests;

use DripTests\support\DripClientTestBase;
use GuzzleHttp\Psr7\Response;

class FormTest extends DripClientTestBase
{
    public function testGetFormsBaseCase()
    {
        $this->client->append(new Response(200, [], '{"blah":"hello"}'));
        $response = $this->client->get_forms();

        $this->assertTrue($response->is_success());
        $this->assertEquals('hello', $response->get_contents()['blah']);
        $this->assertRequest('http://api.example.com/v9001/12345/forms');
    }

    public function testFetchFormBaseCase()
    {
        $this->client->append(new Response(200, [], '{"blah":"hello"}'));
        $response = $this->client->fetch_form('1234');

        $this->assertTrue($response->is_success());
        $this->assertEquals('hello', $response->get_contents()['blah']);
        $this->assertRequest('http://api.example.com/v9001/12345/forms/1234');
    }

}
