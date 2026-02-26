<?php
if (!defined('ABSPATH')) exit;

class StripeCreateCheckoutSessionService
{
    public static function create_session(\WP_REST_Request $request)
    {
        $secret = self::get_secret_key();
        if (!$secret) {
            return new \WP_Error('stripe_secret_missing', 'Stripe secret key not configured.', ['status' => 500]);
        }

        $amount = (int) $request->get_param('amount');
        $currency = strtolower((string) ($request->get_param('currency') ?: 'eur'));
        $order_id = $request->get_param('order_id');
        $email = $request->get_param('email');
        $success_url = $request->get_param('success_url');
        $cancel_url = $request->get_param('cancel_url');

        $payload = [
            'mode' => 'payment',
            'payment_method_types[]' => 'card',
            'line_items[0][quantity]' => 1,
            'line_items[0][price_data][currency]' => $currency,
            'line_items[0][price_data][unit_amount]' => $amount,
            'line_items[0][price_data][product_data][name]' => 'Order Payment',
        ];

        if ($order_id) {
            $payload['client_reference_id'] = (string) $order_id;
            $payload['metadata[order_id]'] = (string) $order_id;
        }

        if ($email) {
            $payload['customer_email'] = $email;
        }

        if ($success_url) {
            $payload['success_url'] = $success_url;
        }

        if ($cancel_url) {
            $payload['cancel_url'] = $cancel_url;
        }

        if (empty($payload['success_url']) || empty($payload['cancel_url'])) {
            return new \WP_Error('stripe_urls_missing', 'Missing success_url or cancel_url.', ['status' => 400]);
        }

        $response = wp_remote_post('https://api.stripe.com/v1/checkout/sessions', [
            'headers' => [
                'Authorization' => 'Bearer ' . $secret,
                'Content-Type' => 'application/x-www-form-urlencoded',
            ],
            'body' => http_build_query($payload),
            'timeout' => 30,
        ]);

        if (is_wp_error($response)) {
            return new \WP_Error('stripe_request_failed', $response->get_error_message(), ['status' => 500]);
        }

        $status = wp_remote_retrieve_response_code($response);
        $body = json_decode(wp_remote_retrieve_body($response), true);

        if ($status < 200 || $status >= 300 || empty($body['url'])) {
            $message = isset($body['error']['message']) ? $body['error']['message'] : 'Stripe error.';
            return new \WP_Error('stripe_error', $message, ['status' => 500, 'stripe' => $body]);
        }

        return new \WP_REST_Response([
            'url' => $body['url'],
            'id' => $body['id'] ?? null,
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
