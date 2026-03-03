<?php
if (!defined('ABSPATH')) exit;

class OrderAcceptOrderFindOrder
{
    public static function run($wpdb, string $orders_table, string $shipping_table, string $order_unique_id): ?array
    {
        $order = $wpdb->get_row($wpdb->prepare("
        SELECT o.id, o.order_unique_id, o.user_id, o.order_title, o.vendor_id, o.sharing_status, o.x, o.y, s.zip_code, s.email, s.name
        FROM {$orders_table} AS o
        LEFT JOIN {$shipping_table} AS s ON s.id = o.shipping_id
        WHERE o.order_unique_id = %s
        LIMIT 1
    ", $order_unique_id), ARRAY_A);

        return is_array($order) ? $order : null;
    }
}
