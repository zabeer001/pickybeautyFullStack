<?php
if (!defined('ABSPATH')) exit;

class StripeWebhookService
{
    public static function webhook(\WP_REST_Request $request)
    {
        $secret = self::get_webhook_secret();
        if (!$secret) {
            return new \WP_Error('stripe_webhook_secret_missing', 'Stripe webhook secret not configured.', ['status' => 500]);
        }

        $payload = $request->get_body();
        $sig_header = isset($_SERVER['HTTP_STRIPE_SIGNATURE']) ? $_SERVER['HTTP_STRIPE_SIGNATURE'] : '';

        $event = self::verify_signature($payload, $sig_header, $secret);
        if (is_wp_error($event)) {
            return $event;
        }

        if (!isset($event['type'])) {
            return new \WP_Error('stripe_invalid_event', 'Invalid Stripe event.', ['status' => 400]);
        }

        if ($event['type'] === 'payment_intent.succeeded') {
            self::handle_payment_intent_succeeded($event);
        }
        if ($event['type'] === 'checkout.session.completed') {
            self::handle_checkout_session_completed($event);
        }

        return new \WP_REST_Response(['received' => true], 200);
    }

    private static function handle_payment_intent_succeeded(array $event): void
    {
        if (!isset($event['data']['object'])) return;
        $pi = $event['data']['object'];
        $order_id = isset($pi['metadata']['order_id']) ? (int) $pi['metadata']['order_id'] : null;
        if (!$order_id) return;

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

    private static function handle_checkout_session_completed(array $event): void
    {
        if (!isset($event['data']['object'])) return;
        $session = $event['data']['object'];
        $order_id = null;

        if (isset($session['metadata']['order_id'])) {
            $order_id = (int) $session['metadata']['order_id'];
        } elseif (isset($session['client_reference_id'])) {
            $order_id = (int) $session['client_reference_id'];
        }

        if (!$order_id) return;

        global $wpdb;
        $orders_table = $wpdb->prefix . 'kib_orders';
        $wpdb->update(
            $orders_table,
            ['payment_status' => 'Paid'],
            ['id' => $order_id],
            ['%s'],
            ['%d']
        );
    }

    private static function verify_signature(string $payload, string $sig_header, string $secret)
    {
        if (!$sig_header) {
            return new \WP_Error('stripe_signature_missing', 'Stripe signature missing.', ['status' => 400]);
        }

        $sig_items = explode(',', $sig_header);
        $timestamp = null;
        $signature = null;

        foreach ($sig_items as $item) {
            $parts = explode('=', $item, 2);
            if (count($parts) !== 2) continue;
            $key = trim($parts[0]);
            $value = trim($parts[1]);
            if ($key === 't') $timestamp = $value;
            if ($key === 'v1') $signature = $value;
        }

        if (!$timestamp || !$signature) {
            return new \WP_Error('stripe_signature_invalid', 'Invalid Stripe signature header.', ['status' => 400]);
        }

        $signed_payload = $timestamp . '.' . $payload;
        $expected = hash_hmac('sha256', $signed_payload, $secret);

        if (!hash_equals($expected, $signature)) {
            return new \WP_Error('stripe_signature_mismatch', 'Stripe signature verification failed.', ['status' => 400]);
        }

        $decoded = json_decode($payload, true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            return new \WP_Error('stripe_invalid_json', 'Invalid JSON payload.', ['status' => 400]);
        }

        return $decoded;
    }

    private static function get_webhook_secret(): ?string
    {
        $env = defined('KIBSTERLP_STRIPE_WEBHOOK_SECRET') ? KIBSTERLP_STRIPE_WEBHOOK_SECRET : null;
        if ($env) return $env;

        $option = get_option('kibsterlp_stripe_webhook_secret');
        if ($option) return $option;

        return null;
    }
}
