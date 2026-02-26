<?php
if (!defined('ABSPATH')) exit;

class OrderUpdateService
{
    public static function update(\WP_REST_Request $request)
    {
        global $wpdb;
        $orders          = $wpdb->prefix . 'kib_orders';
        $customers_table = $wpdb->prefix . 'kib_customers';
        $shipping_table  = $wpdb->prefix . 'kib_shipping_addresses';
        $id     = (int) $request['id'];

        $data  = [];
        $types = [];
        $payment_status_compare  = null;
        $sharing_status_compare  = null;
        $customer_updated        = null;
        $customer_cancel_updated = null;

        $map = [
            'vendor_id'      => '%d',
            'x'              => '%f',
            'y'              => '%f',
            'price'          => '%f',
            'shipping_id'    => '%d',
            'budget'         => '%f',
            'order_title'    => '%s',
            'sharing_status' => '%s',
            'payment_status' => '%s',
        ];

        foreach ($map as $key => $type) {
            if (null !== $request->get_param($key)) {
                $val = $request->get_param($key);
                if ($type === '%s') {
                    $val = sanitize_text_field($val);
                }
                $data[$key] = $val;
                $types[]    = $type;
            }
        }
        // Try to fetch customer email based on this order
        $email = $wpdb->get_var(
            $wpdb->prepare(
                "SELECT c.email
                         FROM {$orders} o
                         LEFT JOIN {$customers_table} c ON o.customer_id = c.id
                         WHERE o.id = %d",
                $id
            )
        );

        // Fallback: try shipping email if customer email is not found
        if (empty($email)) {
            $email = $wpdb->get_var(
                $wpdb->prepare(
                    "SELECT s.email
                             FROM {$orders} o
                             LEFT JOIN {$shipping_table} s ON o.shipping_id = s.id
                             WHERE o.id = %d",
                    $id
                )
            );
        }

        // Compare payment_status with existing value if provided
        if (array_key_exists('payment_status', $data)) {
            $current_payment_status = $wpdb->get_var(
                $wpdb->prepare("SELECT payment_status FROM {$orders} WHERE id = %d", $id)
            );

            if ($current_payment_status !== $data['payment_status'] && $data['payment_status'] === 'paid') {
                // NOT same and payment status is paid
                $payment_status_compare = 'not same and payment status is paid';

                if (!empty($email) && is_email($email)) {
                    $customer_updated = self::updateCustomerOrderCompleteCount($email);
                }
            }
        }

        // Compare sharing_status with existing value if provided
        if (array_key_exists('sharing_status', $data)) {
            $current_sharing_status = $wpdb->get_var(
                $wpdb->prepare("SELECT sharing_status FROM {$orders} WHERE id = %d", $id)
            );

            if ($current_sharing_status !== $data['sharing_status'] && $data['sharing_status'] === 'cancelled') {
                // NOT same and sharing status is cancelled
                $sharing_status_compare = 'not same and sharing status is cancelled';

                if (!empty($email) && is_email($email)) {
                    $customer_cancel_updated = self::updateCustomerOrderCancelledCount($email);
                }
            }
        }

        $shippingObj = self::sanitizeShippingInput($request->get_param('shipping'));
        if ($shippingObj) {
            $current_shipping_id = null;

            if (!empty($data['shipping_id'])) {
                $current_shipping_id = (int) $data['shipping_id'];
            } else {
                $current_shipping_id = (int) $wpdb->get_var(
                    $wpdb->prepare("SELECT shipping_id FROM {$orders} WHERE id = %d", $id)
                );
            }

            if ($current_shipping_id) {
                $res = self::updateShipping($current_shipping_id, $shippingObj);
                if (is_wp_error($res)) return $res;
            } else {
                $newShipId = self::insertShipping($shippingObj);
                if (is_wp_error($newShipId)) return $newShipId;
                $data['shipping_id'] = (int) $newShipId;
                $types[] = '%d';
            }
        }

        if (empty($data)) {
            return new \WP_Error('kib_order_no_changes', 'No fields to update', ['status' => 400]);
        }

        $data['updated_at'] = current_time('mysql');
        $types[] = '%s';

        $ok = $wpdb->update($orders, $data, ['id' => $id], $types, ['%d']);

        if ($ok === false) {
            return new \WP_Error('kib_order_update_failed', 'Failed to update order', ['status' => 500]);
        }

        return new \WP_REST_Response([
            'status'                => true,
            'message'               => 'Order updated successfully',
            'updated'               => (int) $ok,
            'order_id'              => $id,
            'payment_status_compare' => $payment_status_compare,
            'sharing_status_compare' => $sharing_status_compare,
            'customer_email_used'    => isset($email) ? $email : null,
            'customer_updated'       => $customer_updated,
            'customer_cancel_updated' => $customer_cancel_updated,
        ], 200);
    }

    private static function sanitizeShippingInput($payload)
    {
        if (!is_array($payload)) return null;
        return [
            'email'    => isset($payload['email'])    ? sanitize_text_field($payload['email'])    : null,
            'phone'    => isset($payload['phone'])    ? sanitize_text_field($payload['phone'])    : null,
            'name'     => isset($payload['name'])     ? sanitize_text_field($payload['name'])     : null,
            'country'  => isset($payload['country'])  ? sanitize_text_field($payload['country'])  : null,
            'city'     => isset($payload['city'])     ? sanitize_text_field($payload['city'])     : null,
            'district' => isset($payload['district']) ? sanitize_text_field($payload['district']) : null,
            'zip_code' => isset($payload['zip_code']) ? sanitize_text_field($payload['zip_code']) : null,
        ];
    }

    private static function insertShipping(array $shipping)
    {
        global $wpdb;
        $table = $wpdb->prefix . 'kib_shipping_addresses';

        $ok = $wpdb->insert($table, [
            'email'      => $shipping['email'],
            'phone'      => $shipping['phone'],
            'name'       => $shipping['name'],
            'country'    => $shipping['country'],
            'city'       => $shipping['city'],
            'district'   => $shipping['district'],
            'zip_code'   => $shipping['zip_code'],
            'created_at' => current_time('mysql'),
            'updated_at' => current_time('mysql'),
        ], ['%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s']);

        if (!$ok) return new \WP_Error('kib_shipping_create_failed', 'Failed to create shipping address', ['status' => 500]);
        return (int) $wpdb->insert_id;
    }

    private static function updateShipping(int $shipping_id, array $shipping)
    {
        global $wpdb;
        $table = $wpdb->prefix . 'kib_shipping_addresses';

        $data  = [];
        $types = [];
        foreach (['email', 'phone', 'name', 'country', 'city', 'district', 'zip_code'] as $f) {
            if (array_key_exists($f, $shipping) && $shipping[$f] !== null) {
                $data[$f] = $shipping[$f];
                $types[]  = '%s';
            }
        }
        if (empty($data)) return 0;

        $data['updated_at'] = current_time('mysql');
        $types[] = '%s';

        $ok = $wpdb->update($table, $data, ['id' => $shipping_id], $types, ['%d']);
        if ($ok === false) return new \WP_Error('kib_shipping_update_failed', 'Failed to update shipping address', ['status' => 500]);
        return (int) $ok;
    }

    private static function updateCustomerOrderCompleteCount($email)
    {
        return self::updateCustomerOrderStats($email, 1, 0);
    }

    private static function updateCustomerOrderCancelledCount($email)
    {
        return self::updateCustomerOrderStats($email, 0, 1);
    }

    private static function updateCustomerOrderStats($email, $deltaComplete, $deltaCancelled)
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

        $current_complete  = isset($customer['order_complete_count']) ? (int) $customer['order_complete_count'] : 0;
        $current_cancelled = isset($customer['order_cancelled_count']) ? (int) $customer['order_cancelled_count'] : 0;

        // New counts after this change
        $new_complete  = $current_complete + (int) $deltaComplete;
        $new_cancelled = $current_cancelled + (int) $deltaCancelled;

        // Calculate completion percentage:
        // - if no cancelled orders, and at least one complete, it's 100%
        // - if no orders at all, 0
        // - otherwise: complete / (complete + cancelled) * 100
        if ($new_cancelled <= 0) {
            $new_percentage = $new_complete > 0 ? 100 : 0;
        } else {
            $total_orders   = $new_complete + $new_cancelled;
            $new_percentage = $total_orders > 0
                ? (int) round(($new_complete / $total_orders) * 100)
                : 0;
        }

        $updated = false;

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

        if ($result !== false && $result > 0) {
            $updated = true;
        }

        return $updated;

        // $updated now indicates whether any customer row was actually updated.
    }
}
