<?php
if (!defined('ABSPATH')) exit;

class OrderUpdateCustomerOrderStats
{
    public static function run($email, int $delta_complete, int $delta_cancelled): bool
    {
        if (empty($email) || !is_email($email)) {
            return false;
        }

        global $wpdb;
        $customers_table = $wpdb->prefix . 'kib_customers';

        $customer = $wpdb->get_row(
            $wpdb->prepare(
                "SELECT order_complete_count, order_cancelled_count
                 FROM {$customers_table}
                 WHERE email = %s",
                $email
            ),
            ARRAY_A
        );

        if (!$customer) {
            return false;
        }

        $current_complete = isset($customer['order_complete_count']) ? (int) $customer['order_complete_count'] : 0;
        $current_cancelled = isset($customer['order_cancelled_count']) ? (int) $customer['order_cancelled_count'] : 0;

        $new_complete = $current_complete + $delta_complete;
        $new_cancelled = $current_cancelled + $delta_cancelled;

        if ($new_cancelled <= 0) {
            $new_percentage = $new_complete > 0 ? 100 : 0;
        } else {
            $total_orders = $new_complete + $new_cancelled;
            $new_percentage = $total_orders > 0 ? (int) round(($new_complete / $total_orders) * 100) : 0;
        }

        $result = $wpdb->query(
            $wpdb->prepare(
                "UPDATE {$customers_table}
                 SET order_complete_count = %d,
                     order_cancelled_count = %d,
                     order_complete_percentage = %d
                 WHERE email = %s",
                $new_complete,
                $new_cancelled,
                $new_percentage,
                $email
            )
        );

        return $result !== false && $result > 0;
    }
}
