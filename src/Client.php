<?php

namespace Drip;

use Drip\Exception\DripException;
use Drip\Exception\InvalidArgumentException;
use Drip\Exception\InvalidApiKeyException;
use Drip\Exception\InvalidAccessTokenException;
use Drip\Exception\InvalidAccountIdException;
use Drip\Exception\UnexpectedHttpVerbException;

/**
 * Drip API
 *
 * @author Svetoslav Marinov (SLAVI)
 */
class Client
{
    const VERSION = '1.4.1';

    /** @var string */
    protected $api_key = '';
    /** @var string */
    protected $access_token = '';
    /** @var string */
    protected $account_id = '';
    /** @var string */
    protected $api_end_point = 'https://api.getdrip.com/v2/';
    /** @var integer */
    protected $timeout = 30;
    /** @var integer */
    protected $connect_timeout = 30;

    /** @var callable|null */
    protected $guzzle_stack_constructor;

    const GET    = "GET";
    const POST   = "POST";
    const DELETE = "DELETE";
    const PUT    = "PUT";

    /**
     * Accepts API key or access token and saves it internally.
     *
     * @param array|string ...$params
     *               * `api_key` e.g. "qsor48ughrjufyu2dadraasfa1212424"
     *               * `access_token` e.g. "daar48ughrjufyu2dadraasfa421121"
     *               * `account_id` e.g. "123456"
     *               * `api_end_point` (mostly for Drip internal testing)
     *               * `guzzle_stack_constructor` (for test suite, may break at any time, do not use)
     * @throws \Exception
     */
    public function __construct(...$params)
    {
        // Deprecated constructor call.
        if (is_string($params[0])) {
            $this->deprecated_constructor(
                $params[0], // api_key
                $params[1], // account_id
                isset($params[2]) ? $params[2] : [] // options
            );

            return;
        }


        if (array_key_exists('access_token', $params[0])) {
            $this->bearer_auth_setup($params[0]['access_token']);
        } else if (array_key_exists('api_key', $params[0])) {
            $this->basic_auth_setup($params[0]['api_key']);
        } else {
            throw new InvalidArgumentException("Missing Drip API key or access token.");
        }

        $this->set_test_options($params[0]);
        $this->set_account_id($params[0]['account_id']);
    }

    /**
     * Accepts API key and stores it internally -- format to be deprecated.
     *
     * @param string $api_key
     * @param string $account_id
     * @param array{api_end_point?:string, guzzle_stack_constructor?:callable} $options
     *               * `api_end_point` (for test suite)
     *               * `guzzle_stack_constructor` (for test suite)
     * @throws \Exception
     */
    protected function deprecated_constructor($api_key, $account_id, $options = [])
    {
        $this->basic_auth_setup($api_key);
        $this->set_account_id($account_id);
        $this->set_test_options($options);
    }

    /**
     * @param string $api_key
     * @throws \Exception
     */
    protected function basic_auth_setup($api_key)
    {
        $api_key = trim($api_key);
        if (empty($api_key) || !preg_match('#^[\w-]+$#si', $api_key)) {
            throw new InvalidApiKeyException("Missing or invalid Drip API key.");
        }

        $this->api_key = $api_key;
    }

    /**
     * @param string $access_token
     * @throws \Exception
     */
    protected function bearer_auth_setup($access_token)
    {
        $access_token = trim($access_token);
        if (empty($access_token) || !preg_match('#^[\w-]+$#si', $access_token)) {
            throw new InvalidAccessTokenException("Missing or invalid Drip access token.");
        }

        $this->access_token = $access_token;
    }

    /**
     * @param string $account_id
     * @throws \Exception
     */
    protected function set_account_id($account_id)
    {
        $account_id = trim($account_id);
        if (empty($account_id) || !preg_match('#^[\w-]+$#si', $account_id)) {
            throw new InvalidAccountIdException("Missing or invalid Drip account ID.");
        }

        $this->account_id = $account_id;
    }

    /**
     * @param array{api_end_point?:string, guzzle_stack_constructor?:callable} $options
     *              * `api_end_point`
     *              * `guzzle_stack_constructor`
     */
    protected function set_test_options($options)
    {
        if (array_key_exists('api_end_point', $options)) {
            $this->api_end_point = $options['api_end_point'];
        }

        // NOTE: For testing. Could break at any time, please do not depend on this.
        if (array_key_exists('guzzle_stack_constructor', $options)) {
            $this->guzzle_stack_constructor = $options['guzzle_stack_constructor'];
        }

        // TODO: allow setting timeouts
    }

    /**
     * Requests the campaigns for the given account.
     *
     * @param array{status?:string} $params Set of arguments
     *                          - status (optional)
     * @return \Drip\ResponseInterface
     * @throw \Drip\Exception\InvalidArgumentException
     */
    public function get_campaigns($params)
    {
        if (
            isset($params['status'])
            && !in_array($params['status'], ['active', 'draft', 'paused', 'all'])
        ) {
            throw new InvalidArgumentException("Invalid campaign status.");
        }

        return $this->make_request("{$this->account_id}/campaigns", $params);
    }

    /**
     * Fetch a campaign for the given account based on it's ID.
     *
     * @param array{campaign_id?:string} $params Set of arguments
     *                          - campaign_id (required)
     * @return \Drip\ResponseInterface
     */
    public function fetch_campaign($params)
    {
        if (empty($params['campaign_id'])) {
            throw new InvalidArgumentException("campaign_id was not specified");
        }

        $campaign_id = $params['campaign_id'];
        unset($params['campaign_id']); // clear it from the params

        return $this->make_request("{$this->account_id}/campaigns/$campaign_id", $params);
    }

    /**
     * Requests the accounts for the given account.
     * Parses the response JSON and returns an array which contains: id, name, created_at etc
     *
     * @return \Drip\ResponseInterface
     */
    public function get_accounts()
    {
        return $this->make_request('accounts');
    }

    /**
     * Sends a request to add a subscriber and returns its record or false.
     *
     * @param array<mixed> $params
     * @return \Drip\ResponseInterface
     */
    public function create_or_update_subscriber($params)
    {
        // The API wants the params to be JSON encoded
        return $this->make_request(
            "{$this->account_id}/subscribers",
            array('subscribers' => [$params]),
            self::POST
        );
    }

    /**
     * Sends a request to add/update a batch (up to 1000) of subscribers.
     *
     * @param array<mixed> $params
     * @return \Drip\ResponseInterface
     */
    public function create_or_update_subscribers($params)
    {
        return $this->make_request(
            "{$this->account_id}/subscribers/batches",
            $params,
            self::POST
        );
    }

    /**
     * Returns info regarding a particular subscriber
     *
     * @param array<mixed> $params
     * @return \Drip\ResponseInterface
     * @throw \Drip\Exception\InvalidArgumentException
     */
    public function fetch_subscriber($params)
    {
        if (!empty($params['subscriber_id'])) {
            $subscriber_id = $params['subscriber_id'];
            unset($params['subscriber_id']); // clear it from the params
        } elseif (!empty($params['email'])) {
            $subscriber_id = $params['email'];
            unset($params['email']); // clear it from the params
        } else {
            throw new InvalidArgumentException("Subscriber ID or Email was not specified. You must specify either Subscriber ID or Email.");
        }

        $subscriber_id = urlencode($subscriber_id);

        return $this->make_request("{$this->account_id}/subscribers/{$subscriber_id}");
    }

    /**
     * Returns info regarding a particular subscriber subscriptions to campaigns.
     *
     * @param array<mixed> $params
     * @return \Drip\ResponseInterface
     * @throw \Drip\Exception\InvalidArgumentException
     */
    public function fetch_subscriber_campaigns($params)
    {
        if (!empty($params['subscriber_id'])) {
            $subscriber_id = $params['subscriber_id'];
            unset($params['subscriber_id']); // clear it from the params
        } elseif (!empty($params['email'])) {
            $subscriber_id = $params['email'];
            unset($params['email']); // clear it from the params
        } else {
            throw new InvalidArgumentException("Subscriber ID or Email was not specified. You must specify either Subscriber ID or Email.");
        }

        $subscriber_id = urlencode($subscriber_id);

        return $this->make_request("{$this->account_id}/subscribers/{$subscriber_id}/campaign_subscriptions");
    }

    /**
     * Returns a list of subscribers
     *
     * @return \Drip\ResponseInterface
     */
    public function fetch_subscribers()
    {
        return $this->make_request("{$this->account_id}/subscribers");
    }

    /**
     * Subscribes a user to a given campaign for a given account.
     *
     * @param array<mixed> $params
     * @return \Drip\ResponseInterface
     * @throw \Drip\Exception\InvalidArgumentException
     */
    public function subscribe_subscriber($params)
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

        // The API wants the params to be JSON encoded.
        $req_params = ['subscribers' => [$params]];

        return $this->make_request(
            "{$this->account_id}/campaigns/{$campaign_id}/subscribers",
            $req_params,
            self::POST
        );
    }

    /**
     * Some keys are removed from the params so they don't get send with the other data to Drip.
     *
     * @param array<mixed> $params
     * @return \Drip\ResponseInterface
     * @throw \Drip\Exception\InvalidArgumentException
     */
    public function unsubscribe_subscriber($params)
    {
        if (!empty($params['subscriber_id'])) {
            $subscriber_id = $params['subscriber_id'];
            unset($params['subscriber_id']); // clear it from the params
        } elseif (!empty($params['email'])) {
            $subscriber_id = $params['email'];
            unset($params['email']); // clear it from the params
        } else {
            throw new InvalidArgumentException("Subscriber ID or Email was not specified. You must specify either Subscriber ID or Email.");
        }

        $subscriber_id = urlencode($subscriber_id);
        return $this->make_request(
            "{$this->account_id}/subscribers/{$subscriber_id}/unsubscribe",
            $params,
            self::POST
        );
    }

    /**
     * This calls DELETE /:account_id/subscribers/:id_or_email to delete a subscriber.
     *
     * @param array<mixed> $params
     * @return \Drip\ResponseInterface
     * @throw \Drip\Exception\InvalidArgumentException
     */
    public function delete_subscriber($params)
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
        return $this->make_request("{$this->account_id}/subscribers/{$subscriber_id}", $params, self::DELETE);
    }

    /**
     * This calls POST /:account_id/tags to add the tag. It just returns some status code no content
     *
     * @param array<mixed> $params
     * @return \Drip\ResponseInterface
     * @throw \Drip\Exception\InvalidArgumentException
     */
    public function tag_subscriber($params)
    {
        if (empty($params['email'])) {
            throw new InvalidArgumentException("Email was not specified");
        }

        if (empty($params['tag'])) {
            throw new InvalidArgumentException("Tag was not specified");
        }

        // The API wants the params to be JSON encoded
        $req_params = ['tags' => [$params]];

        return $this->make_request("{$this->account_id}/tags", $req_params, self::POST);
    }

    /**
     * This calls DELETE /:account_id/tags to remove the tags. It just returns some status code no content
     *
     * @param array $params
     * @return \Drip\ResponseInterface
     * @throw \Drip\Exception\InvalidArgumentException
     */
    public function untag_subscriber($params)
    {
        if (empty($params['email'])) {
            throw new InvalidArgumentException("Email was not specified");
        }

        if (empty($params['tag'])) {
            throw new InvalidArgumentException("Tag was not specified");
        }

        // The API wants the params to be JSON encoded
        $req_params = ['tags' => [$params]];

        return $this->make_request("{$this->account_id}/tags", $req_params, self::DELETE);
    }

    /**
     * Posts an event specified by the user.
     *
     * @param array $params
     * @return \Drip\ResponseInterface
     * @throw \Drip\Exception\InvalidArgumentException
     */
    public function record_event($params)
    {
        if (empty($params['action'])) {
            throw new InvalidArgumentException("Action was not specified");
        }

        // The API wants the params to be JSON encoded
        $req_params = ['events' => [$params]];

        return $this->make_request("{$this->account_id}/events", $req_params, self::POST);
    }

    /**
     * @return string
     */
    protected function user_agent()
    {
        return "Drip API PHP Wrapper (getdrip.com). Version " . self::VERSION;
    }

    /**
     * Determines whether the response is a success.
     *
     * @param int $code
     * @return boolean
     */
    protected function is_success_response($code)
    {
        return $code >= 200 && $code <= 299;
    }

    /**
     * Send a request.
     *
     * @param string $url
     * @param array<mixed> $params
     * @param string $req_method
     * @return \Drip\ResponseInterface
     * @throws \Exception
     */
    protected function make_request($url, $params = [], $req_method = self::GET)
    {
        if ($this->guzzle_stack_constructor) {
            // This can be replaced with `($this->guzzle_stack_constructor)()` once we drop PHP5 support.
            $fn = $this->guzzle_stack_constructor;
            $stack = $fn();
        } else {
            // @codeCoverageIgnoreStart
            $stack = \GuzzleHttp\HandlerStack::create();
            // @codeCoverageIgnoreEnd
        }

        $client = new \GuzzleHttp\Client([
            'base_uri' => $this->api_end_point,
            'handler' => $stack,
        ]);

        $req_params = [
            'timeout' => $this->timeout,
            'connect_timeout' => $this->connect_timeout,
            'headers' => [
                'User-Agent' => $this->user_agent(),
                'Accept' => 'application/json, text/javascript, */*; q=0.01',
                'Content-Type' => 'application/vnd.api+json',
            ],
            'http_errors' => false,
        ];

        if (!empty($this->api_key)) {
            $req_params['auth'] = [$this->api_key, ''];
        } else if (!empty($this->access_token)) {
            $req_params['headers']['Authorization'] = 'Bearer ' . $this->access_token;
        }

        switch ($req_method) {
            case self::GET:
                $req_params['query'] = $params;
                break;
            case self::POST:
            case self::DELETE:
            // @codeCoverageIgnoreStart
            case self::PUT:
            // @codeCoverageIgnoreEnd
                $req_params['body'] = is_array($params) ? json_encode($params) : $params;
                break;
            default:
                // @codeCoverageIgnoreStart
                throw new UnexpectedHttpVerbException("Unexpected HTTP verb $req_method");
                // @codeCoverageIgnoreEnd
        }

        $res = $client->request($req_method, $url, $req_params);

        return $this->is_success_response($res->getStatusCode())
            ? new \Drip\SuccessResponse($url, $params, $res)
            : new \Drip\ErrorResponse($url, $params, $res);
    }
}
