<?php
namespace Drip\Resources;

use Drip\Exception\InvalidArgumentException;
use GuzzleHttp\Exception\GuzzleException;

trait Form
{
    /**
     * List all forms
     * @return \Drip\ResponseInterface
     * @throws InvalidArgumentException
     * @throws GuzzleException
     */
    public static function get_forms()
    {
        return self::make_request(self::$account_id."/forms");
    }

    /**
     * Fetch a form
     * @param array|string $params Set of arguments
     *                          - form_id (required)
     * @return \Drip\ResponseInterface
     * @throws InvalidArgumentException
     * @throws GuzzleException
     */
    public static function fetch_form($params)
    {
        list($form_id, $params) = self::unwrapParam($params, 'form_id');

        return self::make_request(self::$account_id."/forms/$form_id");
    }
}