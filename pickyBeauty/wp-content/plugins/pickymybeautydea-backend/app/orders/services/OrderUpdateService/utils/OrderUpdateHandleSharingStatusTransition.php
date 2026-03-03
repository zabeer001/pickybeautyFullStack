<?php
if (!defined('ABSPATH')) exit;

class OrderUpdateHandleSharingStatusTransition
{
    public static function run($wpdb, string $orders_table, int $id, array $data, ?string $email): array
    {
        $sharing_status_compare = null;
        $customer_cancel_updated = null;

        if (!array_key_exists('sharing_status', $data)) {
            return [
                'sharing_status_compare' => $sharing_status_compare,
                'customer_cancel_updated' => $customer_cancel_updated,
            ];
        }

        $current_sharing_status = $wpdb->get_var(
            $wpdb->prepare("SELECT sharing_status FROM {$orders_table} WHERE id = %d", $id)
        );

        if ($current_sharing_status !== $data['sharing_status'] && $data['sharing_status'] === 'cancelled') {
            $sharing_status_compare = 'not same and sharing status is cancelled';
            if (!empty($email) && is_email($email)) {
                $customer_cancel_updated = OrderUpdateCustomerOrderCancelledCount::run($email);
            }
        }

        return [
            'sharing_status_compare' => $sharing_status_compare,
            'customer_cancel_updated' => $customer_cancel_updated,
        ];
    }
}
