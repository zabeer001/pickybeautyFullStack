<?php
if (!defined('ABSPATH')) exit;

class OrderCreateBuildSuccessResponse
{
    public static function run(int $id, string $order_unique_id, ?int $shipping_id, array $email_result): \WP_REST_Response
    {
        return new \WP_REST_Response([
            'status' => true,
            'message' => 'Order placed successfully!',
            'data' => [
                'id' => $id,
                'order_unique_id' => $order_unique_id,
                'shipping_id' => $shipping_id,
                'email' => $email_result,
            ],
        ], 201);
    }
}
