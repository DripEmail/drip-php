<?php

namespace Drip;

/**
 * A reason for a response failure.
 */
class Error
{
    /** @var string */
    protected $code;
    /** @var string */
    protected $message;

    /**
     * @param string $code      Coded error reason
     * @param string $message   Human readable error message.
     */
    public function __construct($code, $message)
    {
        $this->code = $code;
        $this->message = $message;
    }

    /**
     * The coded error reason.
     *
     * @return string
     */
    public function get_code()
    {
        return $this->code;
    }

    /**
     * The human readable error message.
     *
     * @return string
     */
    public function get_message()
    {
        return $this->message;
    }
}
