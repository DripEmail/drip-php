<?php

namespace DripTests;

use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Exception\RequestException;

class GuzzleHelpers
{
    /**
     * Helper for mocking the client
     *
     * @param array $history_object
     * @param \GuzzleHttp\Psr7\Response[] $responses
     * @return \Drip\Client
     */
    public static function mocked_client(&$history_object, $responses)
    {
        return new \Drip\Client([
            'account_id' => '12345',
            'api_key' => 'abc123',
            'api_end_point' => 'http://api.example.com/v9001/',
            'guzzle_stack_constructor' => function () use (&$history_object, $responses) {
                $mock = new MockHandler($responses);
                $stack = \GuzzleHttp\HandlerStack::create($mock);
                $stack->push(\GuzzleHttp\Middleware::history($history_object));
                return $stack;
            }
        ]);
    }
}
