<?php
namespace Drip\Resources;

use Drip\Exception\InvalidArgumentException;
use GuzzleHttp\Exception\GuzzleException;


trait CustomFields
{
    /**
     * List all custom field identifiers used in an account
     * @return \Drip\ResponseInterface
     * @throws InvalidArgumentException
     * @throws GuzzleException
     */
    public static function get_custom_fields()
    {
        return self::make_request(self::$account_id."/custom_field_identifiers");
    }
}