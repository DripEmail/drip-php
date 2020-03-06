<?php
namespace Drip\Resources;

use Drip\Exception\InvalidArgumentException;
use GuzzleHttp\Exception\GuzzleException;

trait User
{
    /**
     * Fetch the authenticated user
     * @return \Drip\ResponseInterface
     * @throws InvalidArgumentException
     * @throws GuzzleException
     */
    public static function fetch_user()
    {
        return self::make_request("user");
    }
}
