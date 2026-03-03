<?php
if (!defined('ABSPATH')) exit;

class OrderVendorMyOrderCount
{
    public static function run($wpdb, string $where_sql, array $where_params): int
    {
        $count_sql = "
            SELECT COUNT(*)
            FROM {$wpdb->prefix}kib_orders AS o
            LEFT JOIN {$wpdb->prefix}kib_shipping_addresses AS s ON o.shipping_id = s.id
            {$where_sql}
        ";

        return (int) $wpdb->get_var($wpdb->prepare($count_sql, $where_params));
    }
}
