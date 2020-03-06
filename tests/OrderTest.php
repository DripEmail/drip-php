<?php

namespace DripTests;

use GuzzleHttp\Psr7\Response;

require_once 'support\DripClientTestBase.php';


class OrderTest extends DripClientTestBase
{

    private $order = [
        'provider' => 'my_custom_platform',
        'email' => 'user@gmail.com',
        'action' => 'placed',
        'occurred_at' => '2019-01-17T20:50:00Z',
        'order_id' => '456445746',
        'order_public_id' => '#5',
        'grand_total' => 22.99,
        'total_discounts' => 5.34,
        'total_taxes' => 1.0,
        'total_fees' => 2.0,
        'total_shipping' => 5.0,
        'currency' => 'USD',
        'order_url' => 'https://mysuperstore.com/order/456445746',
        'items' => [
            [
                'product_id' => 'B01J4SWO1G',
                'product_variant_id' => 'B01J4SWO1G-CW-BOTT',
                'sku' => 'XHB-1234',
                'name' => 'The Coolest Water Bottle',
                'brand' => 'Drip',
                'categories' => ['Accessories'],
                'price' => 11.16,
                'sale_price' => 10.16,
                'quantity' => 2,
                'discounts' => 5.34,
                'taxes' => 1.0,
                'fees' => 0.5,
                'shipping' => 5.0,
                'total' => 23.99,
                'product_url' => 'https://mysuperstore.com/dp/B01J4SWO1G',
                'image_url' => 'https://www.getdrip.com/images/example_products/water_bottle.png',
                'product_tag' => 'Best Seller',
            ]
        ],
        'billing_address' => [
            'label' => 'Primary Billing',
            'first_name' => 'Bill',
            'last_name' => 'Billington',
            'company' => 'Bills R US',
            'address_1' => '123 Bill St.',
            'address_2' => 'Apt. B',
            'city' => 'Billtown',
            'state' => 'CA',
            'postal_code' => '01234',
            'country' => 'United States',
            'phone' => '555-555-5555',
        ],
        'shipping_address' => [
            'label' => 'Downtown Office',
            'first_name' => 'Ship',
            'last_name' => 'Shipington',
            'company' => 'Shipping 4 Less',
            'address_1' => '123 Ship St.',
            'city' => 'Shipville',
            'state' => 'CA',
            'postal_code' => '01234',
            'country' => 'United States',
            'phone' => '555-555-5555',
        ],
    ];


    public function testCreateOrUpdateOrderBaseCase()
    {
        $this->client->append(new Response(200, [], '{"blah":"hello"}'));

        $response = $this->client->create_order_activity_event($this->order);

        $this->assertTrue($response->is_success());
        $this->assertEquals('hello', $response->get_contents()['blah']);
        $expectedBody = json_encode($this->order);
        $this->assertRequest('https://api.getdrip.com/v3/12345/shopper_activity/order', 'POST', $expectedBody);
    }

    public function testCreateOrUpdateOrdersBaseCase()
    {
        $this->client->append(new Response(200, [], '{"blah":"hello"}'));

        $response = $this->client->create_order_activity_events([$this->order, $this->order]);

        $this->assertTrue($response->is_success());
        $this->assertEquals('hello', $response->get_contents()['blah']);
        $expectedBody = json_encode(['orders' => [$this->order, $this->order]]);
        $this->assertRequest('https://api.getdrip.com/v3/12345/shopper_activity/order/batch', 'POST', $expectedBody);
    }


}
