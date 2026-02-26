<?php
if (!defined('ABSPATH')) exit;

class StripeCreateIntentService
{
    public static function create_intent(\WP_REST_Request $request)
    {
        $secret = self::get_secret_key();
        if (!$secret) {
            return new \WP_Error('stripe_secret_missing', 'Stripe secret key not configured.', ['status' => 500]);
        }

        $amount = (int) $request->get_param('amount');
        $currency = $request->get_param('currency') ?: 'eur';
        $order_id = $request->get_param('order_id');
        $email = $request->get_param('email');

        $payload = [
            'amount' => $amount,
            'currency' => strtolower((string) $currency),
            'payment_method_types[]' => 'card',
            'metadata[order_id]' => $order_id ? (string) $order_id : '',
        ];

        if ($email) {
            $payload['receipt_email'] = $email;
        }

        $response = wp_remote_post('https://api.stripe.com/v1/payment_intents', [
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

        if ($status < 200 || $status >= 300 || !isset($body['client_secret'])) {
            $message = isset($body['error']['message']) ? $body['error']['message'] : 'Stripe error.';
            return new \WP_Error('stripe_error', $message, ['status' => 500, 'stripe' => $body]);
        }

        return new \WP_REST_Response([
            'client_secret' => $body['client_secret'],
            'payment_intent_id' => $body['id'] ?? null,
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
