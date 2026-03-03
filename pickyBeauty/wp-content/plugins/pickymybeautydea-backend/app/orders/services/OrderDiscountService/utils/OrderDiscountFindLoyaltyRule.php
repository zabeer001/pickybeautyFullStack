<?php
if (!defined('ABSPATH')) exit;

class OrderDiscountFindLoyaltyRule
{
    public static function run($wpdb, int $order_complete_count, int $order_complete_percentage): ?array
    {
        $loyalty_rule = $wpdb->get_row(
            $wpdb->prepare(
                "SELECT * FROM {$wpdb->prefix}kib_loyalty
                 WHERE status = %s
                   AND %d >= min_order
                   AND %d <= max_order
                   AND %d >= order_complete_percentage
                 ORDER BY discount DESC, id ASC
                 LIMIT 1",
                'active',
                $order_complete_count,
                $order_complete_count,
                $order_complete_percentage
            ),
            ARRAY_A
        );

        return is_array($loyalty_rule) ? $loyalty_rule : null;
    }
}
