<?php
if (!defined('ABSPATH')) exit;

class StripeCheckoutStatusService
{
    public static function check_status(\WP_REST_Request $request)
    {
        $secret = self::get_secret_key();
        if (!$secret) {
            return new \WP_Error('stripe_secret_missing', 'Stripe secret key not configured.', ['status' => 500]);
        }

        $session_id = $request->get_param('session_id');
        if (!$session_id) {
            return new \WP_Error('stripe_session_missing', 'Missing session_id.', ['status' => 400]);
        }

        $response = wp_remote_get('https://api.stripe.com/v1/checkout/sessions/' . rawurlencode($session_id), [
            'headers' => [
                'Authorization' => 'Bearer ' . $secret,
                'Content-Type' => 'application/x-www-form-urlencoded',
            ],
            'timeout' => 30,
        ]);

        if (is_wp_error($response)) {
            return new \WP_Error('stripe_request_failed', $response->get_error_message(), ['status' => 500]);
        }

        $status = wp_remote_retrieve_response_code($response);
        $body = json_decode(wp_remote_retrieve_body($response), true);

        if ($status < 200 || $status >= 300 || !is_array($body)) {
            $message = isset($body['error']['message']) ? $body['error']['message'] : 'Stripe error.';
            return new \WP_Error('stripe_error', $message, ['status' => 500, 'stripe' => $body]);
        }

        $payment_status = $body['payment_status'] ?? null;
        $order_id = null;
        if (isset($body['metadata']['order_id'])) {
            $order_id = (int) $body['metadata']['order_id'];
        } elseif (isset($body['client_reference_id'])) {
            $order_id = (int) $body['client_reference_id'];
        }

        if ($payment_status === 'paid' && $order_id) {
            global $wpdb;
            $orders_table = $wpdb->prefix . 'kib_orders';
            $wpdb->update(
                $orders_table,
                ['payment_status' => 'paid'],
                ['id' => $order_id],
                ['%s'],
                ['%d']
            );
        }

        return new \WP_REST_Response([
            'session_id' => $session_id,
            'payment_status' => $payment_status,
            'order_id' => $order_id,
        ], 200);
    }

    private static function get_secret_key(): ?string
    {
        $env = defined('KIBSTERLP_STRIPE_SECRET') ? KIBSTERLP_STRIPE_SECRET : null;
        if ($env) return $env;

        $option = get_option('kibsterlp_stripe_secret');
        if ($option) return $option;

        return null;
    }
}
