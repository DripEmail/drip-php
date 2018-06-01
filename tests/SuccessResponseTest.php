<?php

use PHPUnit\Framework\TestCase;

final class SuccessResponseTest extends TestCase
{
    public function testNormalErrors()
    {
        $response = new \GuzzleHttp\Psr7\Response(200, [], '{"errors":[{"code":"blah","message":"all the blah"}]}', '1.1');
        $err_response = new \Drip\SuccessResponse('http://www.example.com/blah', ['blah' => 'bar'], $response);
        $this->assertTrue($err_response->is_success());
        $this->assertEquals('http://www.example.com/blah', $err_response->get_url());
        $this->assertEquals(['blah' => 'bar'], $err_response->get_params());
        $this->assertEquals(200, $err_response->get_http_code());
        $this->assertEquals('OK', $err_response->get_http_message());
    }
}
