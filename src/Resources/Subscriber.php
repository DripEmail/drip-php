<?php

namespace Drip\Resources;

use Drip\ErrorResponse;
use Drip\Exception\InvalidArgumentException;
use Drip\ResponseInterface;
use Respect\Validation\Exceptions\ValidationException;
use Respect\Validation\Validator as v;

trait Subscriber
{
    /**
     * @param $subscriber
     * @throws InvalidArgumentException
     */
    private static function validate_subscriber($subscriber)
    {
        try {
            v::oneOf(
                v::key('email'),
                v::key('id'),
                v::key('visitor_uuid')
            )->AllOf(
                v::key('email', v::email(), false),
                v::key('id', null, false),
                v::key('visitor_uuid', null, false),
                v::key('new_email', v::email(), false),
                v::key('first_name', v::stringType(), false),
                v::key('last_name', v::stringType(), false),
                v::key('address1', v::stringType(), false),
                v::key('address2', v::stringType(), false),
                v::key('city', v::stringType(), false),
                v::key('state', v::stringType(), false),
                v::key('zip', v::stringType(), false),
                v::key('country', v::stringType(), false),
                v::key('phone', null, false),
                v::key('user_id', null, false),
                v::key('time_zone', v::stringType(), false),
                v::key('lifetime_value', v::numeric(), false),
                v::key('ip_address', v::ip(), false),
                v::key('custom_fields', v::objectType(), false),
                v::key('tags', v::arrayType(), false),
                v::key('remove_tags', v::arrayType(), false),
                v::key('prospect', v::boolType(), false),
                v::key('base_lead_score', v::intType(), false),
                v::key('eu_consent', v::in(['granted', 'denied']), false),
                v::key('eu_consent_message', v::stringType(), false),
                v::key('status', v::in(['active', 'unsubscribed']), false)
            )->assert($subscriber);
        } catch (ValidationException $e) {
            throw new InvalidArgumentException($e->getFullMessage());
        }//end try
    }

    /**
     * Sends a request to add a subscriber and returns its record or false
     *
     * @param array $params
     * @return \Drip\ResponseInterface
     * @throws \GuzzleHttp\Exception\GuzzleException|InvalidArgumentException
     */
    public static function create_or_update_subscriber($params)
    {
        self::validate_subscriber($params);

        // The API wants the params to be JSON encoded
        return self::make_request(
            self::$account_id."/subscribers",
            array('subscribers' => array($params)),
            self::POST
        );
    }

    /**
     * @param array $subscribers
     * @return \Drip\ResponseInterface
     * @throws \GuzzleHttp\Exception\GuzzleException|InvalidArgumentException
     */
    public static function batch_create_or_update_subscribers($subscribers)
    {
        $subscribersBatches = array_chunk($subscribers, 1000, true);
        foreach ($subscribersBatches as $subscribersBatch) {
            foreach ($subscribersBatch as $subscriber) {
                self::validate_subscriber($subscriber);
            }

            $p = ['batches' => [['subscribers' => $subscribersBatch]]];

            $result = self::make_request(self::$account_id."/subscribers/batches", $p, self::POST);
            if ($result instanceof ErrorResponse) {
                return $result;
            }
        }

        return $result; //Assume success;
    }

    /**
     * Sends a request to add/update a batch (up to 1000) of subscribers
     *
     * @param array $params
     * @return \Drip\ResponseInterface
     */
    public static function create_or_update_subscribers($params)
    {
        return self::make_request(
            self::$account_id."/subscribers/batches",
            $params,
            self::POST
        );
    }

    /**
     * Returns info regarding a particular subscriber
     *
     * @param array $params
     * @return \Drip\ResponseInterface
     * @throws InvalidArgumentException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public static function fetch_subscriber($params)
    {
        if (!empty($params['subscriber_id'])) {
            $subscriber_id = $params['subscriber_id'];
            unset($params['subscriber_id']); // clear it from the params
        } else if (!empty($params['email'])) {
            $subscriber_id = $params['email'];
            unset($params['email']); // clear it from the params
        } else {
            throw new InvalidArgumentException("Subscriber ID or Email was not specified. You must specify either Subscriber ID or Email.");
        }

        $subscriber_id = urlencode($subscriber_id);

        return self::make_request(self::$account_id."/subscribers/$subscriber_id");
    }

    /**
     * Returns a list of subscribers
     *
     * @return \Drip\ResponseInterface
     */
    public static function fetch_subscribers()
    {

        return self::make_request(self::$account_id."/subscribers");
    }

    /**
     * Returns a list of subscribers (alias)
     *
     * @return \Drip\ResponseInterface
     */
    public static function get_subscribers()
    {

        return self::fetch_subscribers();
    }

    /**
     * Subscribes a user to a given campaign for a given account.
     *
     * @param array $params
     * @return ResponseInterface
     * @throws InvalidArgumentException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public static function subscribe_subscriber($params)
    {
        if (empty($params['campaign_id'])) {
            throw new InvalidArgumentException("Campaign ID not specified");
        }

        $campaign_id = $params['campaign_id'];
        unset($params['campaign_id']); // clear it from the params

        if (empty($params['email'])) {
            throw new InvalidArgumentException("Email not specified");
        }

        if (!isset($params['double_optin'])) {
            $params['double_optin'] = true;
        }

        // The API wants the params to be JSON encoded
        $req_params = array('subscribers' => array($params));

        return self::make_request(self::$account_id."/campaigns/$campaign_id/subscribers", $req_params, self::POST);
    }


    /**
     * @param array|mixed $subscriber_id
     * @return mixed
     */
    public static function campaign_subscriptions($subscriber_id)
    {
        list($subscriber_id, $params) = self::unwrapParam($subscriber_id, 'subscriber_id');
        $subscriber_id = urlencode($subscriber_id);
        return self::make_request(self::$account_id."/subscribers/$subscriber_id/campaign_subscriptions");
    }

    /**
     *
     * Some keys are removed from the params so they don't get send with the other data to Drip.
     *
     * @param array $params
     * @return ResponseInterface
     * @throws InvalidArgumentException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public static function unsubscribe_subscriber($params)
    {
        if (!empty($params['subscriber_id'])) {
            $subscriber_id = $params['subscriber_id'];
            unset($params['subscriber_id']); // clear it from the params
        } else if (!empty($params['email'])) {
            $subscriber_id = $params['email'];
            unset($params['email']); // clear it from the params
        } else {
            throw new InvalidArgumentException("Subscriber ID or Email was not specified. You must specify either Subscriber ID or Email.");
        }

        $subscriber_id = urlencode($subscriber_id);
        return self::make_request(self::$account_id."/subscribers/$subscriber_id/unsubscribe", $params, self::POST);
    }

}