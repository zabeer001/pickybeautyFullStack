<?php
if (!defined('ABSPATH')) exit;

class OrderUpdateResolveOrderEmail
{
    public static function run($wpdb, string $orders_table, string $customers_table, string $shipping_table, int $id): ?string
    {
        $email = $wpdb->get_var(
            $wpdb->prepare(
                "SELECT c.email
                 FROM {$orders_table} o
                 LEFT JOIN {$customers_table} c ON o.customer_id = c.id
                 WHERE o.id = %d",
                $id
            )
        );

        if (!empty($email)) {
            return $email;
        }

        $email = $wpdb->get_var(
            $wpdb->prepare(
                "SELECT s.email
                 FROM {$orders_table} o
                 LEFT JOIN {$shipping_table} s ON o.shipping_id = s.id
                 WHERE o.id = %d",
                $id
            )
        );

        return $email ?: null;
    }
}
