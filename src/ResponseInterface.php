<?php

namespace Drip;

interface ResponseInterface
{
    public function is_success();
    public function get_url();
    public function get_params();
    public function get_http_code();
    public function get_http_message();
}
