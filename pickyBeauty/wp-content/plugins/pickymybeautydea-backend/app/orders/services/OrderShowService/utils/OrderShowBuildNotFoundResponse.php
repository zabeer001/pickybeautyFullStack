<?php
if (!defined('ABSPATH')) exit;

class OrderShowBuildNotFoundResponse
{
    public static function run(): \WP_REST_Response
    {
        return new \WP_REST_Response([
            'status' => false,
            'message' => 'Order not found',
            'data' => null,
        ], 404);
    }
}
