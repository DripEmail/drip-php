<?php
use PHPUnit\Framework\TestCase;

final class ClientTest extends TestCase
{
    public function testInitializedWithApiToken()
    {
        $client = new \Drip\Client("abc123", "1234");
        $this->assertInstanceOf(\Drip\Client::class, $client);
    }

    public function testInvalidApiToken()
    {
        $this->expectException(Drip\Exception\InvalidApiTokenException::class);
        new \Drip\Client("", "1234");
    }

    public function testInvalidAccountId()
    {
        $this->expectException(Drip\Exception\InvalidAccountIdException::class);
        new \Drip\Client("abc123", "");
    }
}
