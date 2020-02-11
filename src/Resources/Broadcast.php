<?php

namespace Drip\Resources;

use Drip\Exception\InvalidArgumentException;
use GuzzleHttp\Exception\GuzzleException;
use Respect\Validation\Validator as v;

trait Broadcast
{
    /**
     * Requests the campaigns for the given account.
     * @param array $params Set of arguments
     *                          - status (optional)
     * @return \Drip\ResponseInterface
     * @throws InvalidArgumentException
     * @throws GuzzleException
     */
    public static function get_broadcasts($params = [])
    {
        $v = v::keySet(
            v::key('status', v::in(array('draft', 'scheduled', 'sent', 'all')), false),
            v::key('sort', v::in(array('created_at', 'send_at', 'name')), false),
            v::key('direction', v::in(array('asc', 'desc')), false)
        );

        return self::make_request(self::$account_id . "/broadcasts", $params, self::GET, $v);
    }

    /**
     * Fetch a broadcast for the given account based on it's ID.
     * @param array|string $params Set of arguments
     *                          - broadcast_id (required)
     * @return \Drip\ResponseInterface
     * @throws InvalidArgumentException
     * @throws GuzzleException
     */
    public static function fetch_broadcast($params)
    {
        list($broadcast_id, $params) = self::unwrapParam($params, 'broadcast_id');

        return self::make_request(self::$account_id . "/broadcasts/$broadcast_id");
    }
}