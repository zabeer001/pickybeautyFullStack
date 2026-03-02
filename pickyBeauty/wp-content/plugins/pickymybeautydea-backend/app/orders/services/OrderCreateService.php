<?php
if (!defined('ABSPATH')) exit;

class OrderCreateService
{
    public static function create(\WP_REST_Request $request)
    {
        global $wpdb;
        $orders = $wpdb->prefix . 'kib_orders';
        $customers_table = $wpdb->prefix . 'kib_customers';

        $user_id     = get_current_user_id();
        $vendor_id   = ($request->get_param('vendor_id') !== null) ? (int) $request->get_param('vendor_id') : null;
        $price       = (float) $request->get_param('budget');
        $budget      = ($request->get_param('budget') !== null) ? (float) $request->get_param('budget') : null;
        $order_title = isset($request) ? sanitize_text_field($request->get_param('order_title')) : null;
        $order_details = isset($request) ? sanitize_text_field($request->get_param('order_details')) : null;
        $payment_method = isset($request) ? sanitize_text_field($request->get_param('payment_method')) : null;
        $sharing     = sanitize_text_field($request->get_param('sharing_status') ?: 'not accepted');
        $category_id = sanitize_text_field($request->get_param('category_id'));
        $shipping_id = ($request->get_param('shipping_id') !== null) ? (int) $request->get_param('shipping_id') : null;
        $shippingObj = self::sanitizeShippingInput($request->get_param('shipping'));
        $x_value     = self::resolveCoordinateValue($request, $shippingObj, 'x');
        $y_value     = self::resolveCoordinateValue($request, $shippingObj, 'y');

        if ($shippingObj) {
            $newShipId = self::insertShipping($shippingObj);
            if (is_wp_error($newShipId)) return $newShipId;
            $shipping_id = (int) $newShipId;
        }

        // Auto-create / link customer by email if provided
        $customer_id = null;
        if (!empty($shippingObj['email']) && is_email($shippingObj['email'])) {
            $email = sanitize_email($shippingObj['email']);

            // Check if customer already exists
            $existing_customer_id = $wpdb->get_var(
                $wpdb->prepare("SELECT id FROM {$customers_table} WHERE email = %s", $email)
            );

            if ($existing_customer_id) {
                $customer_id = (int) $existing_customer_id;
            } else {
                // Create new customer with defaults for stats columns
                $ok_customer = $wpdb->insert(
                    $customers_table,
                    [
                        'name'       => isset($shippingObj['name']) ? sanitize_text_field($shippingObj['name']) : '',
                        'email'      => $email,
                        'phone'      => isset($shippingObj['phone']) ? sanitize_text_field($shippingObj['phone']) : null,
                        'created_at' => current_time('mysql'),
                        'updated_at' => current_time('mysql'),
                    ],
                    ['%s', '%s', '%s', '%s', '%s']
                );

                if ($ok_customer) {
                    $customer_id = (int) $wpdb->insert_id;
                }
            }
        }

        $order_unique_id = wp_generate_uuid4();

        $data = [
            'user_id'         => $user_id ?: null,
            'vendor_id'       => $vendor_id ?: null,
            'customer_id'     => $customer_id ?: null,
            'price'           => $price,
            'shipping_id'     => $shipping_id ?: null,
            'budget'          => $budget,
            'order_title'     => $order_title ?: null,
            'order_details'   => $order_details ?: null,
            'payment_method'  => $payment_method ?: null,
            'order_unique_id' => $order_unique_id,
            'sharing_status'  => $sharing,
            'category_id'     => (int) $category_id,
            'x'               => $x_value,
            'y'               => $y_value,
            'created_at'      => current_time('mysql'),
            'updated_at'      => current_time('mysql'),
        ];

        $formats = ['%d', '%d', '%d', '%f', '%d', '%f', '%s', '%s', '%s', '%s', '%s', '%d', '%f', '%f', '%s', '%s'];

        $ok = $wpdb->insert($orders, $data, $formats);

        if (!$ok) {
            return new \WP_Error('kib_order_create_failed', 'Failed to create order', [
                'status'    => 500,
                'db_error'  => $wpdb->last_error,
                'db_query'  => $wpdb->last_query,
                'db_data'   => $data,
            ]);
        }

        $id = (int) $wpdb->insert_id;

        $email_result = self::sendOrderConfirmationEmail($id, $order_unique_id, $order_title, $shippingObj, $user_id);

        return new \WP_REST_Response([
            'status'  => true,
            'message' => 'Order placed successfully!',
            'data'    => [
                'id'              => $id,
                'order_unique_id' => $order_unique_id,
                'shipping_id'     => $shipping_id,
                'email'           => $email_result,
            ],
        ], 201);
    }

    private static function sanitizeShippingInput($payload)
    {
        if (is_string($payload)) {
            $decoded = json_decode($payload, true);
            if (json_last_error() === JSON_ERROR_NONE) {
                $payload = $decoded;
            }
        }

        if (!is_array($payload)) return null;
        return [
            'email'    => isset($payload['email'])    ? sanitize_text_field($payload['email'])    : null,
            'phone'    => isset($payload['phone'])    ? sanitize_text_field($payload['phone'])    : null,
            'name'     => isset($payload['name'])     ? sanitize_text_field($payload['name'])     : null,
            'country'  => isset($payload['country'])  ? sanitize_text_field($payload['country'])  : null,
            'city'     => isset($payload['city'])     ? sanitize_text_field($payload['city'])     : null,
            'district' => isset($payload['district']) ? sanitize_text_field($payload['district']) : null,
            'zip_code' => isset($payload['zip_code']) ? sanitize_text_field($payload['zip_code']) : null,
            'x'        => self::sanitizeCoordinateValue($payload['x'] ?? $payload['lat'] ?? $payload['latitude'] ?? null),
            'y'        => self::sanitizeCoordinateValue($payload['y'] ?? $payload['lng'] ?? $payload['longitude'] ?? null),
        ];
    }

    private static function resolveCoordinateValue(\WP_REST_Request $request, ?array $shipping, string $axis): ?float
    {
        $request_value = self::sanitizeCoordinateValue($request->get_param($axis));
        if ($request_value !== null) {
            return $request_value;
        }

        if (is_array($shipping) && array_key_exists($axis, $shipping)) {
            return self::sanitizeCoordinateValue($shipping[$axis]);
        }

        return null;
    }

    private static function sanitizeCoordinateValue($value): ?float
    {
        if ($value === null || $value === '') {
            return null;
        }

        if (!is_numeric($value)) {
            return null;
        }

        return (float) $value;
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

    private static function sendOrderConfirmationEmail(
        int $order_id,
        string $order_unique_id,
        ?string $order_title,
        ?array $shipping,
        int $user_id
    ): array {
        $to = '';
        if (!empty($shipping['email']) && is_email($shipping['email'])) {
            $to = $shipping['email'];
        } elseif ($user_id) {
            $user = get_user_by('id', $user_id);
            if ($user && is_email($user->user_email)) {
                $to = $user->user_email;
            }
        }

        if ($to === '') {
            return [
                'sent' => false,
                'reason' => 'No recipient email found.',
            ];
        }

        $site_name = wp_specialchars_decode(get_bloginfo('name'), ENT_QUOTES);
        $subject = sprintf('Your order is confirmed - %s', $site_name);
        $message = self::buildOrderEmailHtml($order_id, $order_unique_id, $order_title, $shipping);

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

    private static function buildOrderEmailHtml(
        int $order_id,
        string $order_unique_id,
        ?string $order_title,
        ?array $shipping
    ): string {
        $site_name = wp_specialchars_decode(get_bloginfo('name'), ENT_QUOTES);
        $safe_title = $order_title ? esc_html($order_title) : 'Your order';
        $name = isset($shipping['name']) && $shipping['name'] ? esc_html($shipping['name']) : 'there';
        $home_url = esc_url(home_url('/'));

        return '
            <div style="font-family: Arial, sans-serif; color: #1f2937; line-height: 1.6;">
                <h2 style="color:#111827; margin-bottom: 8px;">Order confirmed</h2>
                <p>Hi ' . $name . ',</p>
                <p>Thanks for your order with <strong>' . esc_html($site_name) . '</strong>. We have received it and it is confirmed.</p>
                <p><strong>Order:</strong> ' . $safe_title . '<br/>
                   <strong>Order ID:</strong> ' . esc_html((string) $order_id) . '</p>
                <p>We will contact you if we need any more details.</p>
                <p style="margin-top: 24px;">Thanks,<br/>' . esc_html($site_name) . ' Team</p>
                <hr style="border:none;border-top:1px solid #e5e7eb;margin:24px 0;">
                <p style="font-size: 12px; color: #6b7280;">Visit us: <a href="' . $home_url . '">' . $home_url . '</a></p>
            </div>
        ';
    }
}
