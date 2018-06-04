<?php

namespace Drip;

/**
 * Base response class
 */
abstract class AbstractResponse implements ResponseInterface
{
    /** @var string */
    protected $url;
    /** @var array */
    protected $params;
    /** @var \Psr\Http\Message\ResponseInterface */
    protected $response;
    /** @var array */
    protected $body;

    public function __construct($url, $params, \Psr\Http\Message\ResponseInterface $response)
    {
        $this->url = $url;
        $this->params = $params;
        $this->response = $response;
        $this->body = json_decode($response->getBody(), true);
    }

    /**
     * Whether the response is successfull.
     *
     * @return boolean
     */
    public function is_success()
    {
        return $this->get_http_code() >= 200 && $this->get_http_code() <= 299;
    }

    /**
     * The url of the request.
     *
     * @return string
     */
    public function get_url()
    {
        return $this->url;
    }

    /**
     * The parameters of the request.
     *
     * @return array
     */
    public function get_params()
    {
        return $this->params;
    }

    /**
     * The http response code
     *
     * @return integer
     */
    public function get_http_code()
    {
        return $this->response->getStatusCode();
    }

    /**
     * The http response message
     *
     * @return string
     */
    public function get_http_message()
    {
        return $this->response->getReasonPhrase();
    }
}
