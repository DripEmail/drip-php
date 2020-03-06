<?php

namespace Drip\Resources;

use Drip\Exception\InvalidArgumentException;
use Drip\ResponseInterface;
use GuzzleHttp\Exception\GuzzleException;
use Respect\Validation\Validator as v;

trait Campaign
{

    /**
     * Requests the campaigns for the given account.
     * @param array $params Set of arguments
     *                          - status (optional)
     * @return \Drip\ResponseInterface
     * @throws InvalidArgumentException
     * @throws GuzzleException
     */
    public static function get_campaigns($params)
    {
        $v = v::keySet(
            v::key('status', v::in(array('active', 'draft', 'paused', 'all')), false),
            v::key('sort', v::in(array('created_at', 'send_at', 'name')), false),
            v::key('direction', v::in(array('asc', 'desc')), false)
        );

        return self::make_request(self::$account_id . "/campaigns", $params, self::GET, $v);
    }

    /**
     * Fetch a campaign for the given account based on it's ID.
     * @param array|string $params Set of arguments
     *                          - campaign_id (required)
     * @return \Drip\ResponseInterface
     * @throws InvalidArgumentException
     * @throws GuzzleException
     */
    public static function fetch_campaign($params)
    {
        list($campaign_id, $params) = self::unwrapParam($params, 'campaign_id');

        return self::make_request(self::$account_id . "/campaigns/$campaign_id", $params);
    }

    /**
     * Activate a campaign
     * @param array|string $params Set of arguments
     *                          - campaign_id (required)
     * @return ResponseInterface
     * @throws InvalidArgumentException
     * @throws GuzzleException
     */
    public static function activate_campaign($params)
    {
        list($campaign_id, $params) = self::unwrapParam($params, 'campaign_id');

        return self::make_request(self::$account_id . "/campaigns/$campaign_id/activate", $params, self::POST);
    }

    /**
     * Pause a campaign
     * @param array|string $params Set of arguments
     *                          - campaign_id (required)
     * @return ResponseInterface
     * @throws InvalidArgumentException
     * @throws GuzzleException
     */
    public static function pause_campaign($params)
    {
        list($campaign_id, $params) = self::unwrapParam($params, 'campaign_id');

        return self::make_request(self::$account_id . "/campaigns/$campaign_id/pause", $params, self::POST);
    }

    /**
     * Get a list of campaign subscribers
     * @param array|string $params Set of arguments
     *                          - campaign_id (required)
     * @return ResponseInterface
     * @throws InvalidArgumentException
     * @throws GuzzleException
     */
    public static function campaign_subscribers($params)
    {
        list($campaign_id, $params) = self::unwrapParam($params, 'campaign_id');

        $v = v::keySet(
            v::key('status', v::in(array('active', 'unsubscribed', 'removed')), false),
            v::key('page', v::intType()->positive(), false),
            v::key('sort', v::in(array('id', 'created_at')), false),
            v::key('direction', v::in(array('asc', 'desc')), false),
            v::key('per_page', v::intType()->between(1, 1000), false)
        );

        return self::make_request(self::$account_id . "/campaigns/$campaign_id/subscribers", $params, self::GET, $v);
    }
}
