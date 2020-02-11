<?php

namespace Drip\Resources;

use Drip\Exception\InvalidArgumentException;
use Drip\ResponseInterface;
use GuzzleHttp\Exception\GuzzleException;
use Respect\Validation\Validator as v;

trait Tag
{
    /**
     * List all tags used in an account
     * @return \Drip\ResponseInterface
     * @throws InvalidArgumentException
     * @throws GuzzleException
     */
    public static function get_tags()
    {
        return self::make_request(self::$account_id . "/tags");
    }


    /**
     *
     * Apply a tag to a subscriber
     * @param array $params
     * @return ResponseInterface
     * @throws InvalidArgumentException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public static function tag_subscriber($params)
    {
        $v = v::keyNested(
            'tags.0',
            v::keySet(
                v::key('email', v::stringType(), true),
                v::key('tag', v::stringType(), true)
            ));
        // The API wants the params to be JSON encoded
        $req_params = array('tags' => array($params));

        return self::make_request(self::$account_id . "/tags", $req_params, self::POST, $v);
    }

    /**
     *
     * This calls DELETE /:account_id/tags to remove the tags. It just returns some status code no content
     *
     * @param array $params
     * @return ResponseInterface
     * @throws InvalidArgumentException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public static function untag_subscriber($params)
    {
        $v = v::keyNested(
            'tags.0',
            v::keySet(
                v::key('email', v::stringType(), true),
                v::key('tag', v::stringType(), true)
            ));

        // The API wants the params to be JSON encoded
        $req_params = array('tags' => array($params));

        return self::make_request(self::$account_id . "/tags", $req_params, self::DELETE, $v);
    }

}