<?php

namespace DripTests;

use DripTests\support\DripClientTestBase;
use GuzzleHttp\Psr7\Response;

class CustomFieldsTest extends DripClientTestBase
{
    public function testGetCustomFieldsBaseCase()
    {
        $this->client->append(new Response(200, [], '{"blah":"hello"}'));
        $response = $this->client->get_custom_fields();

        $this->assertTrue($response->is_success());
        $this->assertEquals('hello', $response->get_contents()['blah']);
        $this->assertRequest('http://api.example.com/v9001/12345/custom_field_identifiers');
    }
}
