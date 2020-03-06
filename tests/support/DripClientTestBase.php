<?php

namespace DripTests\support;

use PHPUnit\Framework\TestCase;
use Psr\Http\Message\RequestInterface;

class DripClientTestBase extends TestCase
{
    /** @var GuzzleHelpers $client */
    protected $client;

    protected function setUp()
    {
        $this->client = new GuzzleHelpers();
    }

    /**
     * @param string $expectedUrl
     * @param string $expectedmethod
     * @param string $expectedBody
     * @param string $expectedBodyType
     * @param RequestInterface $actualRequest
     */
    public function assertRequest($expectedUrl, $expectedmethod = 'GET', $expectedBody = null, $expectedBodyType = 'JSON', $actualRequest = null)
    {
        if ($actualRequest === null) {
            $actualRequest = $this->client->getLastRequest();
        }

        $this->assertEquals($expectedUrl, $actualRequest->getUri());
        $this->assertEquals($expectedmethod, $actualRequest->getMethod());
        if ($expectedBody !== null) {
            switch ($expectedBodyType) {
                case 'JSON':
                    $this->assertJsonStringEqualsJsonString($expectedBody, (string) $actualRequest->getBody());
                    break;
                default:
                    $this->assertEquals($expectedBody, $actualRequest->getBody());
                    break;
            }
        }
    }
}
