<?php
if (!defined('ABSPATH')) exit;

class OrderUserMyOrderCount
{
    public static function run($wpdb, string $user_email): int
    {
        $count_sql = "
            SELECT COUNT(*)
            FROM {$wpdb->prefix}kib_orders AS o
            LEFT JOIN {$wpdb->prefix}kib_shipping_addresses AS s ON s.id = o.shipping_id
            WHERE s.email = %s
        ";

        return (int) $wpdb->get_var($wpdb->prepare($count_sql, $user_email));
    }
}
