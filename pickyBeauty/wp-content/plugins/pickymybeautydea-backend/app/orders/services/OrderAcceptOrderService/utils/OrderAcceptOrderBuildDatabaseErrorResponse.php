<?php
if (!defined('ABSPATH')) exit;

class OrderAcceptOrderBuildDatabaseErrorResponse
{
    public static function run(): \WP_REST_Response
    {
        return new \WP_REST_Response([
            'status' => false,
            'message' => 'Database error while updating order status.',
        ], 500);
    }
}
