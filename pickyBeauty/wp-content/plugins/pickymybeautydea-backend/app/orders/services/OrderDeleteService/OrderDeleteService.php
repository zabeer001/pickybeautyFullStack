<?php
if (!defined('ABSPATH')) exit;

class OrderDeleteService
{
    public static function delete(\WP_REST_Request $request)
    {
        global $wpdb;
        $orders    = $wpdb->prefix . 'kib_orders';
        $shippings = $wpdb->prefix . 'kib_shipping_addresses';
        $id        = (int) $request['id'];
        $order_id  = $id;

        $shipping_id = $wpdb->get_var(
            $wpdb->prepare("SELECT shipping_id FROM {$orders} WHERE id = %d", $order_id)
        );

        $shipping_id = (int) $shipping_id;

        if ($shipping_id) {
            $shipping = $wpdb->delete($shippings, ['id' => $shipping_id], ['%d']);

            if (!$shipping) {
                return new \WP_Error('kib_shipping_delete_failed', 'Failed to delete order', ['status' => 500]);
            }
        }

        $order = $wpdb->delete($orders, ['id' => $id], ['%d']);

        if (!$order) {
            return new \WP_Error('kib_order_delete_failed', 'Failed to delete order', ['status' => 500]);
        }

        return new \WP_REST_Response(['deleted' => (int) $order], 200);
    }
}
