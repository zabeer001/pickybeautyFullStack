<?php
if (!defined('ABSPATH')) exit;

class OrderUpdateBuildNoChangesError
{
    public static function run(): \WP_Error
    {
        return new \WP_Error('kib_order_no_changes', 'No fields to update', ['status' => 400]);
    }
}
