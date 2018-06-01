<?php

namespace Drip;

class ErrorResponse implements ResponseInterface {
    /** @var string */
    private $url;
    /** @var array */
    private $params;
    /** @var \Psr\Http\Message\ResponseInterface */
    private $response;
    /** @var array */
    private $body;

    public function __construct($url, $params, \Psr\Http\Message\ResponseInterface $response) {
        $this->url = $url;
        $this->params = $params;
        $this->response = $response;
        $this->body = json_decode($response->getBody(), true);
    }

    public function get_errors() {
        if (!empty($this->body['errors'])) { // JSON
            return array_map(function($rec) {
                return new Error($rec['code'], $rec['message']);
            }, $this->body['errors']);
        } else {
            return [];
        }
    }

    /**
     * Whether the response is successfull.
     *
     * @return boolean
     */
    public function is_success() {
        return false;
    }

    /**
     * The url of the request.
     *
     * @return string
     */
    public function get_url() {
        return $this->url;
    }

    /**
     * The parameters of the request.
     *
     * @return array
     */
    public function get_params() {
        return $this->params;
    }

    /**
     * The http response code
     *
     * @return integer
     */
    public function get_http_code() {
        return $this->response->getStatusCode();
    }

    /**
     * The http response message
     *
     * @return string
     */
    public function get_http_message() {
        return $this->response->getReasonPhrase();
    }
}
