<?php

namespace Drip;

/**
 * A successful response
 */
class SuccessResponse extends AbstractResponse
{
    /**
     * API Response contents
     *
     * @return array
     */
    public function get_contents()
    {
        return $this->body;
    }
}
