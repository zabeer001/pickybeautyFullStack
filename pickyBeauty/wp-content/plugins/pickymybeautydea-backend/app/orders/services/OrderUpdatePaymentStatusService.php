<?php
if (!defined('ABSPATH')) exit;

class OrderUpdatePaymentStatusService
{
    public static function update_payment_status(\WP_REST_Request $request)
    {
        global $wpdb;

        $orders_table = $wpdb->prefix . 'kib_orders';
        $order_id     = (int) $request['id'];
        $status       = sanitize_text_field($request['payment_status']);

        $order_exists = $wpdb->get_var(
            $wpdb->prepare("SELECT COUNT(*) FROM {$orders_table} WHERE id = %d", $order_id)
        );

        if (!$order_exists) {
            return new \WP_Error('kib_order_not_found', 'payment not found.', ['status' => 404]);
        }

        $updated = $wpdb->update(
            $orders_table,
            ['payment_status' => $status],
            ['id' => $order_id],
            ['%s'],
            ['%d']
        );

        if ($updated === false) {
            return new \WP_Error('kib_payment_update_failed', 'Failed to update payment status.', ['status' => 500]);
        }

        return new \WP_REST_Response([
            'id'             => $order_id,
            'payment_status' => $status,
            'message'        => $updated ? 'Order status updated successfully.' : 'No change — status already set.',
        ], 200);
    }
}
