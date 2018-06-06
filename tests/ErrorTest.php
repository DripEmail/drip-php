<?php

namespace DripTests;

use PHPUnit\Framework\TestCase;

final class ErrorTest extends TestCase
{
    public function testNormalError()
    {
        $error = new \Drip\Error("blahcode", "blah all the things");
        $this->assertEquals("blahcode", $error->get_code());
        $this->assertEquals("blah all the things", $error->get_message());
    }
}
