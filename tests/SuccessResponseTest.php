<?php

namespace DripTests;

use PHPUnit\Framework\TestCase;

final class SuccessResponseTest extends TestCase
{
    public function testNormalErrors()
    {
        $response = new \GuzzleHttp\Psr7\Response(200, [], '{"errors":[{"code":"blah","message":"all the blah"}]}', '1.1');
        $succ_response = new \Drip\SuccessResponse('http://www.example.com/blah', ['blah' => 'bar'], $response);
        $this->assertTrue($succ_response->is_success());
        $this->assertEquals('http://www.example.com/blah', $succ_response->get_url());
        $this->assertEquals(['blah' => 'bar'], $succ_response->get_params());
        $this->assertEquals(200, $succ_response->get_http_code());
        $this->assertEquals('OK', $succ_response->get_http_message());
    }

    public function testContents()
    {
        $response = new \GuzzleHttp\Psr7\Response(200, [], '{"blah":"stuff!!!"}', '1.1');
        $succ_response = new \Drip\SuccessResponse('http://www.example.com/blah', ['blah' => 'bar'], $response);
        $this->assertEquals("stuff!!!", $succ_response->get_contents()['blah']);
    }
}
