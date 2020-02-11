<?php
namespace Drip\Resources;

use Drip\Exception\InvalidArgumentException;
use GuzzleHttp\Exception\GuzzleException;
use Respect\Validation\Validator as v;

trait Conversion
{
    /**
     * List all conversions.
     * @param array $params Set of arguments
     *                          - status (optional)
     * @return \Drip\ResponseInterface
     * @throws InvalidArgumentException
     * @throws GuzzleException
     */
    public static function get_conversions($params = [])
    {
        $v = v::keySet(
            v::key('status', v::in(array('active', 'disabled', 'all')), false),
            v::key('sort', v::in(array('created_at', 'name')), false),
            v::key('direction', v::in(array('asc', 'desc')), false)
        );

        return self::make_request(self::$account_id."/goals", $params, self::GET, $v);
    }

    /**
     * Fetch a conversion
     * @param array|string $params Set of arguments
     *                          - conversion_id (required)
     * @return \Drip\ResponseInterface
     * @throws InvalidArgumentException
     * @throws GuzzleException
     */
    public static function fetch_conversion($params)
    {
        list($conversion_id, $params) = self::unwrapParam($params, 'conversion_id');

        return self::make_request(self::$account_id."/goals/$conversion_id");
    }
}