<?php

namespace Drip;

/**
 * Error response
 */
class ErrorResponse extends AbstractResponse
{
    /**
     * Array of errors from the response.
     *
     * @return \Drip\Error[]
     */
    public function get_errors()
    {
        if (!empty($this->body['errors'])) { // JSON
            return array_map(function ($rec) {
                return new Error($rec['code'], $rec['message']);
            }, $this->body['errors']);
        } else {
            return [];
        }
    }
}
