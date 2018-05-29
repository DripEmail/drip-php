<?php
use PHPUnit\Framework\TestCase;

final class ClientTest extends TestCase
{
    public function testInitializedWithApiToken()
    {
        $client = new \Drip\Client('1234');
        $this->assertInstanceOf(\Drip\Client::class, $client);
    }
}
