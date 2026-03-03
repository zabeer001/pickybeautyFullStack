<?php
if (!defined('ABSPATH')) exit;

class OrderAcceptOrderPersistAcceptance
{
    public static function run($wpdb, string $orders_table, string $order_unique_id, string $normalized_status, int $user_id)
    {
        return $wpdb->update(
            $orders_table,
            [
                'sharing_status' => $normalized_status,
                'vendor_id' => $user_id,
                'updated_at' => current_time('mysql'),
            ],
            ['order_unique_id' => $order_unique_id],
            ['%s', '%d', '%s'],
            ['%s']
        );
    }
}
