<?php
if (!defined('ABSPATH')) exit;

class OrderUserMyOrderBuildNoEmailResponse
{
    public static function run(): \WP_REST_Response
    {
        return new \WP_REST_Response([
            'status' => true,
            'message' => 'No email associated with the current user.',
            'current_page' => 1,
            'per_page' => 0,
            'total_orders' => 0,
            'total_pages' => 0,
            'orders' => [],
        ], 200);
    }
}
