<?php
if (!defined('ABSPATH')) exit;

class OrderCreateInsertOrder
{
    public static function run(array $data)
    {
        global $wpdb;

        $formats = ['%d', '%d', '%d', '%f', '%d', '%f', '%s', '%s', '%s', '%s', '%s', '%d', '%f', '%f', '%s', '%s'];
        $ok = $wpdb->insert($wpdb->prefix . 'kib_orders', $data, $formats);

        if (!$ok) {
            return new \WP_Error('kib_order_create_failed', 'Failed to create order', [
                'status' => 500,
                'db_error' => $wpdb->last_error,
                'db_query' => $wpdb->last_query,
                'db_data' => $data,
            ]);
        }

        return (int) $wpdb->insert_id;
    }
}
