<?php
if (!defined('ABSPATH')) exit;

class OrderDiscountBuildValidationErrorResponse
{
    public static function run(array $error): \WP_REST_Response
    {
        return new \WP_REST_Response([
            'success' => false,
            'message' => $error['message'],
            'status' => $error['status'],
        ], $error['status']);
    }
}
