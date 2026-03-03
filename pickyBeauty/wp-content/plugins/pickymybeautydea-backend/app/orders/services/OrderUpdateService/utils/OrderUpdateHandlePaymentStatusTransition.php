<?php
if (!defined('ABSPATH')) exit;

class OrderUpdateHandlePaymentStatusTransition
{
    public static function run($wpdb, string $orders_table, int $id, array $data, ?string $email): array
    {
        $payment_status_compare = null;
        $customer_updated = null;

        if (!array_key_exists('payment_status', $data)) {
            return [
                'payment_status_compare' => $payment_status_compare,
                'customer_updated' => $customer_updated,
            ];
        }

        $current_payment_status = $wpdb->get_var(
            $wpdb->prepare("SELECT payment_status FROM {$orders_table} WHERE id = %d", $id)
        );

        if ($current_payment_status !== $data['payment_status'] && $data['payment_status'] === 'paid') {
            $payment_status_compare = 'not same and payment status is paid';
            if (!empty($email) && is_email($email)) {
                $customer_updated = OrderUpdateCustomerOrderCompleteCount::run($email);
            }
        }

        return [
            'payment_status_compare' => $payment_status_compare,
            'customer_updated' => $customer_updated,
        ];
    }
}
