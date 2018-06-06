<?php

namespace DripTests;

use PHPUnit\Framework\TestCase;

final class ErrorResponseTest extends TestCase
{
    public function testNormalErrors()
    {
        $response = new \GuzzleHttp\Psr7\Response(400, [], '{"errors":[{"code":"blah","message":"all the blah"}]}', '1.1');
        $err_response = new \Drip\ErrorResponse('http://www.example.com/blah', ['blah' => 'bar'], $response);
        $errors = $err_response->get_errors();
        $this->assertContainsOnlyInstancesOf(\Drip\Error::class, $errors);
        $this->assertEquals("blah", $errors[0]->get_code());
        $this->assertEquals("all the blah", $errors[0]->get_message());
        $this->assertFalse($err_response->is_success());
        $this->assertEquals('http://www.example.com/blah', $err_response->get_url());
        $this->assertEquals(['blah' => 'bar'], $err_response->get_params());
        $this->assertEquals(400, $err_response->get_http_code());
        $this->assertEquals('Bad Request', $err_response->get_http_message());
    }

    public function testHTMLResponse()
    {
        $response = new \GuzzleHttp\Psr7\Response(500, [], '<!doctype html><html><head></head><body><p>Error message</p></body></html>', '1.1');
        $client = new \Drip\ErrorResponse("http://www.example.com/blah", ['blah' => 'bar'], $response);
        $this->assertCount(0, $client->get_errors());
    }

    public function testMissingErrors()
    {
        $response = new \GuzzleHttp\Psr7\Response(400, [], '{}', '1.1');
        $client = new \Drip\ErrorResponse("http://www.example.com/blah", ['blah' => 'bar'], $response);
        $this->assertCount(0, $client->get_errors());
    }
}
