<?php
if (!defined('ABSPATH')) exit;

class OrderUpdateBuildUpdateFailedError
{
    public static function run(): \WP_Error
    {
        return new \WP_Error('kib_order_update_failed', 'Failed to update order', ['status' => 500]);
    }
}
