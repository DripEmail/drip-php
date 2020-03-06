<?php

namespace Drip;

use Drip\Exception\InvalidAccountIdException;
use Drip\Exception\InvalidApiTokenException;
use Drip\Exception\InvalidArgumentException;
use Drip\Exception\UnexpectedHttpVerbException;
use Drip\Exception\UnitializedException;
use Drip\Resources\Account;
use Drip\Resources\Broadcast;
use Drip\Resources\Campaign;
use Drip\Resources\Conversion;
use Drip\Resources\CustomFields;
use Drip\Resources\Event;
use Drip\Resources\Form;
use Drip\Resources\Order;
use Drip\Resources\Subscriber;
use Drip\Resources\Tag;
use Drip\Resources\User;
use Exception;
use GuzzleHttp\Exception\GuzzleException;
use Respect\Validation\Exceptions\ValidationException;
use Respect\Validation\Validator as v;

/**
 * Drip API
 * @author Svetoslav Marinov (SLAVI)
 */
class Client implements DripAPIInterface
{
    use UnwrapParam, Account, Broadcast, Campaign, CustomFields, Conversion, Subscriber, Event, Form, Tag, User, Order;

    /** @var string */
    protected static $api_token = '';
    /** @var string */
    protected static $account_id = '';
    /** @var string */
    protected static $api_end_point = 'https://api.getdrip.com/v2/';
    /** @var integer */
    protected static $timeout = 30;
    /** @var integer */
    protected static $connect_timeout = 30;
    /** @var boolean */
    protected static $initialised = false;

    /** @var callable */
    protected static $guzzle_stack_constructor;


    /**
     * @param v $validator
     * @param array $params
     * @throws InvalidArgumentException
     */
    protected static function validate_params($validator, $params)
    {
        try {
            $validator->assert($params);
        } catch (ValidationException $e) {
            throw new InvalidArgumentException($e->getFullMessage());
        }
    }

    /**
     * @param string $api_token
     * @param string $account_id
     * @param string $api_end_point
     * @param callable $guzzle_stack_constructor
     * @param int $timeout
     * @param int $connect_timeout
     * @throws InvalidApiTokenException|InvalidAccountIdException
     */
    public static function init_client($api_token = null, $account_id = null, $api_end_point = 'https://api.getdrip.com/v2/', $guzzle_stack_constructor = null, $timeout = 30, $connect_timeout = 30)
    {
        if ($api_token === null) {
            $api_token = getenv('DRIP_API_TOKEN');
        }

        if (empty($api_token) || !preg_match('#^[\w-]+$#si', $api_token)) {
            throw new InvalidApiTokenException("Missing or invalid Drip API token.");
        }

        if ($account_id === null) {
            $account_id = getenv('DRIP_ACCOUNT_ID');
        }

        if (empty($account_id) || !preg_match('#^[\w-]+$#si', $account_id)) {
            throw new InvalidAccountIdException("Missing or invalid Drip API token.");
        }

        self::$api_token = $api_token;
        self::$account_id = $account_id;
        self::$api_end_point = $api_end_point;
        self::$guzzle_stack_constructor = $guzzle_stack_constructor;
        self::$timeout = $timeout;
        self::$connect_timeout = $connect_timeout;
        self::$initialised = true;
    }

    public static function is_initialized()
    {
        return self::$initialised;
    }

    /**
     *
     * @param string $url
     * @param array $params
     * @param $req_method
     * @param v $validator
     * @param $endpoint_version
     * @return \Drip\ResponseInterface
     * @throws InvalidArgumentException|UnexpectedHttpVerbException|GuzzleException|UnitializedException
     */
    protected static function make_request($url, $params = array(), $req_method = DripAPIInterface::GET, $validator = null, $endpoint_version = 2)
    {
        // Check if we have initialized all the variables
        if (!self::is_initialized()) {
            try {
                self::init_client(); //try to initialise the client
            } catch (\Exception $e) {
                throw new UnitializedException("Drip Client not initialized properly");
            }
            if (!self::is_initialized()) { // still not initialised
                throw new UnitializedException("Drip Client not initialized properly");
            }
        }


        if ($validator === null) {
            $validator = v::alwaysValid();
        }

        self::validate_params($validator, $params);

        if (self::$guzzle_stack_constructor) {
            // This can be replaced with `($this->guzzle_stack_constructor)()` once we drop PHP5 support.
            $fn = self::$guzzle_stack_constructor;
            $stack = $fn();
        } else {
            // @codeCoverageIgnoreStart
            $stack = \GuzzleHttp\HandlerStack::create();
            // @codeCoverageIgnoreEnd
        }

        $endpoint = $endpoint_version == 2 ? self::$api_end_point : 'https://api.getdrip.com/v3/';

        $client = new \GuzzleHttp\Client([
            'base_uri' => $endpoint,
            'handler' => $stack,
        ]);

        $req_params = [
            'auth' => [self::$api_token, ''],
            'timeout' => self::$timeout,
            'connect_timeout' => self::$connect_timeout,
            'headers' => [
                'User-Agent' => self::user_agent(),
                'Accept' => 'application/json, text/javascript, */*; q=0.01',
                'Content-Type' => 'application/vnd.api+json',
            ],
            'http_errors' => false,
        ];

        switch ($req_method) {
            case DripAPIInterface::GET:
                $req_params['query'] = $params;
                break;
            case DripAPIInterface::POST:
            case DripAPIInterface::DELETE:
                // @codeCoverageIgnoreStart
            case DripAPIInterface::PUT:
                // @codeCoverageIgnoreEnd
                $req_params['body'] = is_array($params) ? json_encode($params) : $params;
                break;
            default:
                // @codeCoverageIgnoreStart
                throw new UnexpectedHttpVerbException("Unexpected HTTP verb $req_method");
                break;
            // @codeCoverageIgnoreEnd
        }

        $res = $client->request($req_method, $url, $req_params);

        $success_klass = self::is_success_response($res->getStatusCode()) ? SuccessResponse::class : ErrorResponse::class;
        return new $success_klass($url, $params, $res);
    }

    /**
     * @return string
     */
    protected static function user_agent()
    {
        return "Drip API PHP Wrapper (getdrip.com). Version " . DripAPIInterface::VERSION;
    }

    /**
     * Determines whether the response is a success.
     *
     * @param int $code
     * @return boolean
     */
    protected static function is_success_response($code)
    {
        return $code >= 200 && $code <= 299;
    }


    /**
     * Accepts the token and saves it internally.
     *
     * @param string $api_token e.g. qsor48ughrjufyu2dadraasfa1212424
     * @param string $account_id e.g. 123456
     * @param array $options
     *               * `api_end_point` (mostly for Drip internal testing)
     *               * `guzzle_stack_constructor` (for test suite, may break at any time, do not use)
     * @throws Exception
     */
    public function __construct($api_token, $account_id, $options = [])
    {
        $api_token = trim($api_token);
        $account_id = trim($account_id);
        self::init_client($api_token, $account_id);
        if (\array_key_exists('api_end_point', $options)) {
            self::$api_end_point = $options['api_end_point'];
        }
        // NOTE: For testing. Could break at any time, please do not depend on this.
        if (\array_key_exists('guzzle_stack_constructor', $options)) {
            self::$guzzle_stack_constructor = $options['guzzle_stack_constructor'];
        }
        // TODO: allow setting timeouts
    }

}
