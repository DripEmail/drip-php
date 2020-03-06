<?php

namespace Drip\Resources;

use Drip\Exception\InvalidArgumentException;
use Drip\ResponseInterface;
use GuzzleHttp\Exception\GuzzleException;
use Respect\Validation\Validator as v;

trait Event
{
    /**
     * List all custom events actions used in an account
     * @param array $params Set of arguments
     *                          - page (optional)
     *                          - per_page (optional)
     * @return \Drip\ResponseInterface
     * @throws InvalidArgumentException
     * @throws GuzzleException
     */
    public static function get_event_actions($params = [])
    {
        $v = v::key('page', v::intVal(), false)
            ->key('per_page', v::intVal()->between(1, 1000), false);

        return self::make_request(self::$account_id . "/event_actions", $params, self::GET, $v);
    }

    /**
     *
     * Posts an event specified by the user.
     *
     * @param array $params
     * @return ResponseInterface
     * @throws InvalidArgumentException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public static function record_event($params)
    {
        $v = v::keyNested('events.0.action', v::stringType(), true)
            ->keyNested('events.0.prospect', v::boolType(), false)
            ->keyNested('events.0.properties', v::arrayType(), false)
            ->keyNested('events.0.occurred_at', v::date(\DateTime::ISO8601), false)
            ->oneOf(
                v::keyNested('events.0.email'),
                v::keyNested('events.0.id')
            );

        // The API wants the params to be JSON encoded
        $req_params = array('events' => array($params));

        return self::make_request(self::$account_id . "/events", $req_params, self::POST, $v);
    }
}
