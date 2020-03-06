<?php

namespace Drip\Resources;

use Drip\DripAPIInterface;
use Drip\ErrorResponse;
use Drip\Exception\InvalidArgumentException;
use GuzzleHttp\Exception\GuzzleException;
use Respect\Validation\Exceptions\ValidationException;
use Respect\Validation\Validator as v;

trait Order
{
    /**
     * Fetch the authenticated user
     * @return \Drip\ResponseInterface
     * @throws InvalidArgumentException
     * @throws GuzzleException
     */
    public static function create_order_activity_event($params)
    {


        return self::make_request(self::$account_id . '/shopper_activity/order', $params, $req_method = DripAPIInterface::POST, Order::get_order_validator(), 3);
    }

    public static function get_order_validator()
    {
        $addressValidator = V::keySet(
            v::key('label', V::stringType(), false),
            v::key('first_name', V::stringType(), false),
            v::key('last_name', V::stringType(), false),
            v::key('company', V::stringType(), false),
            v::key('address_1', V::stringType(), false),
            v::key('address_2', V::stringType(), false),
            v::key('city', V::stringType(), false),
            v::key('state', V::stringType(), false),
            v::key('postal_code', V::stringType(), false),
            v::key('country', V::stringType(), false),
            v::key('phone', V::stringType(), false)
        );

        $itemsValidator = V::arrayVal()->each(V::allOf(
            v::key('product_id', null, false),
            v::key('product_variant_id', null, false),
            v::key('sku', null, false),
            v::key('name', V::stringType(), true),
            v::key('brand', V::stringType(), false),
            v::key('categories', V::arrayType(), false),
            v::key('price', V::floatType(), true),
            v::key('quantity', V::intType(), false),
            v::key('discounts', V::floatType(), false),
            v::key('taxes', V::floatType(), false),
            v::key('fees', V::floatType(), false),
            v::key('shipping', V::floatType(), false),
            v::key('total', V::floatType(), false),
            v::key('product_url', V::stringType(), false),
            v::key('image_url', V::stringType(), false)
        ));

        return (
        v::oneOf(
            v::key('email'),
            v::key('person_id')
        )->AllOf(
            v::key('provider', v::stringType(), true),
            v::key('action', V::in(['placed', 'updated', 'paid', 'fulfilled', 'refunded', 'canceled']), true),
            v::key('occurred_at', V::date(\DateTime::ISO8601), false),
            v::key('new_email', v::email(), false),
            v::key('order_id', v::stringType(), true),
            v::key('order_public_id', v::stringType(), false),
            v::key('grand_total', v::floatType(), false),
            v::key('total_discounts', v::floatType(), false),
            v::key('total_taxes', v::floatType(), false),
            v::key('total_fees', v::floatType(), false),
            v::key('total_shipping', v::floatType(), false),
            v::key('refund_amount', v::floatType(), false),
            v::key('currency', V::currencyCode(), false),
            v::key('order_url', V::stringType(), false),
            v::key('items', $itemsValidator, true),
            v::key('billing_address', $addressValidator, false),
            v::key('shipping_address', $addressValidator, false)
        ));
    }

    public static function create_order_activity_events($orders)
    {
        $orderBatches = array_chunk($orders, 1000, true);
        foreach ($orderBatches as $orderBatch) {
            foreach ($orderBatch as $order) {
                try {
                    self::get_order_validator()->assert($order);
                } catch (ValidationException $e) {
                    throw new InvalidArgumentException($e->getFullMessage());
                }
            }

            $p = ['orders' => $orderBatch];

            $result = self::make_request(self::$account_id . "/shopper_activity/order/batch", $p, self::POST, null, 3);
            if ($result instanceof ErrorResponse) {
                return $result;
            }
        }

        return $result; //Assume success;
    }
}
