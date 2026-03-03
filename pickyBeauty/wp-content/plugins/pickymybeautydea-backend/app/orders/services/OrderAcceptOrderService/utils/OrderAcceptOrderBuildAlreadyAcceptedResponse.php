<?php
if (!defined('ABSPATH')) exit;

class OrderAcceptOrderBuildAlreadyAcceptedResponse
{
    public static function run(): \WP_REST_Response
    {
        return new \WP_REST_Response([
            'status' => false,
            'message' => 'This order has already been accepted by another vendor.',
        ], 400);
    }
}
