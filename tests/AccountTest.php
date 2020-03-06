<?php

namespace DripTests;

use GuzzleHttp\Psr7\Response;

require_once 'support\DripClientTestBase.php';

class AccountTest extends DripClientTestBase
{

    public function testGetAccountsBaseCase()
    {
        $this->client->append(new Response(200, [], '{"blah":"hello"}'));

        $response = $this->client->get_accounts();
        $this->assertTrue($response->is_success());
        $this->assertEquals('hello', $response->get_contents()['blah']);
        $this->assertRequest('http://api.example.com/v9001/accounts');
    }

    // #fetch_account
    public function testFetchAccountBaseCase()
    {
        $this->client->append(new Response(200, [], '{"blah":"hello"}'));
        $response = $this->client->fetch_account(['account_id' => '1234']);

        $this->assertTrue($response->is_success());
        $this->assertEquals('hello', $response->get_contents()['blah']);
        $this->assertRequest('http://api.example.com/v9001/accounts/1234');
    }

    public function testFetchAccountWithAccountIDBaseCase()
    {
        $this->client->append(new Response(200, [], '{"blah":"hello"}'));
        $response = $this->client->fetch_account(1234);

        $this->assertTrue($response->is_success());
        $this->assertEquals('hello', $response->get_contents()['blah']);
        $this->assertRequest('http://api.example.com/v9001/accounts/1234');
    }

    public function testFetchAccountMissingId()
    {
        $this->client->append(new Response(200, [], '{"blah":"hello"}'));
        $this->expectException(\Drip\Exception\InvalidArgumentException::class);
        $this->client->fetch_account([]);
    }
}
