<?php
if (!defined('ABSPATH')) exit;

class OrderAcceptOrderService
{
    public static function acceptOrder(\WP_REST_Request $request)
    {
        global $wpdb;

        $orders_table   = $wpdb->prefix . 'kib_orders';
        $shipping_table = $wpdb->prefix . 'kib_shipping_addresses';

        $order_unique_id = sanitize_text_field($request['order_unique_id']);
        $status          = sanitize_text_field($request['sharing_status']);

        $current_user = wp_get_current_user();
        if (!$current_user || !in_array('vendor', (array) $current_user->roles, true)) {
            return new \WP_REST_Response([
                'status'  => false,
                'message' => 'Unauthorized. Only vendors can accept orders.',
            ], 403);
        }

        $user_id = $current_user->ID;
        $user_zipcode = get_user_meta($user_id, 'zipcode', true);

        if (empty($user_zipcode)) {
            return new \WP_REST_Response([
                'status'  => false,
                'message' => 'No zipcode found in your profile. Please contact admin.',
            ], 400);
        }

        $order = $wpdb->get_row($wpdb->prepare("
        SELECT o.id, o.order_unique_id, o.user_id, o.order_title, o.vendor_id, o.sharing_status, s.zip_code, s.email, s.name
        FROM {$orders_table} AS o
        LEFT JOIN {$shipping_table} AS s ON s.id = o.shipping_id
        WHERE o.order_unique_id = %s
        LIMIT 1
    ", $order_unique_id), ARRAY_A);

        if (!$order) {
            return new \WP_REST_Response([
                'status'  => false,
                'message' => 'Order not found.',
            ], 404);
        }

        if (trim($order['zip_code']) !== trim($user_zipcode)) {
            return new \WP_REST_Response([
                'status'        => false,
                'message'       => 'You can only accept orders in your zipcode area.',
                'user_zipcode'  => $user_zipcode,
                'order_zipcode' => $order['zip_code'],
            ], 403);
        }

        if (strtolower($order['sharing_status']) === 'accepted') {
            return new \WP_REST_Response([
                'status'  => false,
                'message' => 'This order has already been accepted by another vendor.',
            ], 400);
        }

        $updated = $wpdb->update(
            $orders_table,
            [
                'sharing_status' => strtolower($status),
                'vendor_id'      => $user_id,
                'updated_at'     => current_time('mysql'),
            ],
            ['order_unique_id' => $order_unique_id],
            ['%s', '%d', '%s'],
            ['%s']
        );

        if ($updated === false) {
            return new \WP_REST_Response([
                'status'  => false,
                'message' => 'Database error while updating order status.',
            ], 500);
        }

        $email_result = self::sendOrderAcceptedEmail(
            (int) $order['id'],
            $order_unique_id,
            $order['order_title'] ?? null,
            $order['email'] ?? null,
            $order['name'] ?? null,
            isset($order['user_id']) ? (int) $order['user_id'] : 0
        );

        return new \WP_REST_Response([
            'status'  => true,
            'message' => 'Order accepted successfully.',
            'data'    => [
                'order_unique_id' => $order_unique_id,
                'vendor_id'       => $user_id,
                'sharing_status'  => strtolower($status),
                'zip_code'        => $user_zipcode,
                'email'           => $email_result,
            ],
        ], 200);
    }

    private static function sendOrderAcceptedEmail(
        int $order_id,
        string $order_unique_id,
        ?string $order_title,
        ?string $shipping_email,
        ?string $shipping_name,
        int $user_id
    ): array {
        $to = '';
        $name = '';

        if (!empty($shipping_email) && is_email($shipping_email)) {
            $to = $shipping_email;
            $name = $shipping_name ? $shipping_name : '';
        } elseif ($user_id) {
            $user = get_user_by('id', $user_id);
            if ($user && is_email($user->user_email)) {
                $to = $user->user_email;
                $name = $user->display_name ? $user->display_name : '';
            }
        }

        if ($to === '') {
            return [
                'sent' => false,
                'reason' => 'No recipient email found.',
            ];
        }

        $site_name = wp_specialchars_decode(get_bloginfo('name'), ENT_QUOTES);
        $subject = sprintf('Your order is accepted - %s', $site_name);
        $message = self::buildOrderAcceptedEmailHtml($order_id, $order_unique_id, $order_title, $name);

        $error_data = null;
        $error_handler = function ($wp_error) use (&$error_data) {
            $error_data = $wp_error;
        };
        add_action('wp_mail_failed', $error_handler);

        $sent = wp_mail($to, $subject, $message, ['Content-Type: text/html; charset=UTF-8']);

        remove_action('wp_mail_failed', $error_handler);

        if (!$sent) {
            $reason = 'Unknown error.';
            $details = null;
            if ($error_data instanceof \WP_Error) {
                $reason = $error_data->get_error_message();
                $details = $error_data->get_error_data();
            }
            return [
                'sent' => false,
                'reason' => $reason,
                'details' => $details,
                'to' => $to,
            ];
        }

        return [
            'sent' => true,
            'to' => $to,
        ];
    }

    private static function buildOrderAcceptedEmailHtml(
        int $order_id,
        string $order_unique_id,
        ?string $order_title,
        ?string $name
    ): string {
        $site_name = wp_specialchars_decode(get_bloginfo('name'), ENT_QUOTES);
        $safe_title = $order_title ? esc_html($order_title) : 'Your order';
        $safe_name = $name ? esc_html($name) : 'there';
        $home_url = esc_url(home_url('/'));

        return '
            <div style="font-family: Arial, sans-serif; color: #1f2937; line-height: 1.6;">
                <h2 style="color:#111827; margin-bottom: 8px;">Order accepted</h2>
                <p>Hi ' . $safe_name . ',</p>
                <p>Your order has been accepted. The vendor will contact you shortly.</p>
                <p><strong>Order:</strong> ' . $safe_title . '<br/>
                   <strong>Order ID:</strong> ' . esc_html((string) $order_id) . '</p>
                <p style="margin-top: 24px;">Thanks,<br/>' . esc_html($site_name) . ' Team</p>
                <hr style="border:none;border-top:1px solid #e5e7eb;margin:24px 0;">
                <p style="font-size: 12px; color: #6b7280;">Visit us: <a href="' . $home_url . '">' . $home_url . '</a></p>
            </div>
        ';
    }
}
