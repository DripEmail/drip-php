<?php

namespace DripTests;

use DripTests\support\DripClientTestBase;
use GuzzleHttp\Psr7\Response;

class UserTest extends DripClientTestBase
{
    public function testGetUsersBaseCase()
    {
        $this->client->append(new Response(200, [], '{"blah":"hello"}'));
        $response = $this->client->fetch_user();

        $this->assertTrue($response->is_success());
        $this->assertEquals('hello', $response->get_contents()['blah']);
        $this->assertRequest('http://api.example.com/v9001/user');
    }

}
