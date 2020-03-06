<?php

namespace DripTests\support;

use Drip\Client;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;

class GuzzleHelpers extends Client
{
    /** @var MockHandler $mock */
    public $mock;

    /** @var array $history */
    public $history = array();

    /**
     * Helper for mocking the client
     *
     * @param array $history_object
     * @param \GuzzleHttp\Psr7\Response[] $responses
     */
    public function __construct()
    {
        $this->mock = new MockHandler();
        $stack = HandlerStack::create($this->mock);
        $stack->push(\GuzzleHttp\Middleware::history($this->history));


        parent::__construct("abc123", 12345, [
            'api_end_point' => 'http://api.example.com/v9001/',
            'guzzle_stack_constructor' => function () use ($stack) {
                return $stack;
            }
        ]);
    }

    /**
     * @return \Psr\Http\Message\UriInterface
     */
    public function getLastUri()
    {
        return $this->mock->getLastRequest()->getUri();
    }

    /**
     * @return \Psr\Http\Message\RequestInterface
     */
    public function getLastRequest()
    {
        return $this->mock->getLastRequest();
    }

    /**
     * @param $a
     */
    public function append($a)
    {
        $this->mock->append($a);
    }
}
