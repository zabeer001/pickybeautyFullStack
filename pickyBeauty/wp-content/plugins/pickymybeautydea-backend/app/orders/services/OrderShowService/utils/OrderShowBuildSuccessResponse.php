<?php
if (!defined('ABSPATH')) exit;

class OrderShowBuildSuccessResponse
{
    public static function run(array $order_data): \WP_REST_Response
    {
        return new \WP_REST_Response([
            'status' => true,
            'message' => 'Order retrieved successfully',
            'data' => $order_data,
        ], 200);
    }
}
