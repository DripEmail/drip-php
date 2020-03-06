<?php

namespace Drip\Resources;

use Drip\Exception\InvalidArgumentException;
use Drip\ResponseInterface;
use GuzzleHttp\Exception\GuzzleException;

trait Account
{
    /**
     * Requests the accounts for the given account.
     * Parses the response JSON and returns an array which contains: id, name, created_at etc
     * @return ResponseInterface
     * @throws GuzzleException
     */
    public static function get_accounts()
    {
        return self::make_request('accounts');
    }

    /**
     * Fetch an account based on it's ID.
     * @param array|string $params Set of arguments or account_id
     *                          - account_id (required)
     * @return ResponseInterface
     * @throws InvalidArgumentException
     * @throws GuzzleException
     */
    public static function fetch_account($params)
    {
        list($account_id, $params) = self::unwrapParam($params, 'account_id');

        return self::make_request("accounts/$account_id", $params);
    }
}
