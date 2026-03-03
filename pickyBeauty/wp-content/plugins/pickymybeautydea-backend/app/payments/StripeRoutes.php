<?php
if (!defined('ABSPATH')) exit;

require_once __DIR__ . '/StripeController.php';

class StripeRoutes
{
    private StripeController $controller;

    public function __construct()
    {
        $this->controller = new StripeController();
    }

    public function register_routes(): void
    {
        register_rest_route('kibsterlp-admin/v1', '/stripe/intent', [
            'methods'  => 'POST',
            'callback' => [$this->controller, 'create_intent'],
            'permission_callback' => '__return_true',
            'args' => [
                'amount' => [
                    'required' => true,
                    'validate_callback' => function ($value) {
                        return is_numeric($value) && $value > 0;
                    },
                    'sanitize_callback' => function ($value) {
                        return (int) $value;
                    },
                ],
                'currency' => [
                    'required' => false,
                    'sanitize_callback' => 'sanitize_text_field',
                ],
                'order_id' => [
                    'required' => false,
                    'validate_callback' => function ($value) {
                        return is_null($value) || is_numeric($value);
                    },
                    'sanitize_callback' => 'absint',
                ],
                'email' => [
                    'required' => false,
                    'validate_callback' => function ($value) {
                        return is_null($value) || is_email($value);
                    },
                    'sanitize_callback' => 'sanitize_email',
                ],
            ],
        ]);

        register_rest_route('kibsterlp-admin/v1', '/stripe/checkout', [
            'methods'  => 'POST',
            'callback' => [$this->controller, 'create_checkout_session'],
            'permission_callback' => '__return_true',
            'args' => [
                'amount' => [
                    'required' => true,
                    'validate_callback' => function ($value) {
                        return is_numeric($value) && $value > 0;
                    },
                    'sanitize_callback' => function ($value) {
                        return (int) $value;
                    },
                ],
                'currency' => [
                    'required' => false,
                    'sanitize_callback' => 'sanitize_text_field',
                ],
                'order_id' => [
                    'required' => false,
                    'validate_callback' => function ($value) {
                        return is_null($value) || is_numeric($value);
                    },
                    'sanitize_callback' => 'absint',
                ],
                'email' => [
                    'required' => false,
                    'validate_callback' => function ($value) {
                        return is_null($value) || is_email($value);
                    },
                    'sanitize_callback' => 'sanitize_email',
                ],
                'success_url' => [
                    'required' => false,
                    'sanitize_callback' => function ($value) {
                        return is_string($value) ? trim($value) : '';
                    },
                ],
                'cancel_url' => [
                    'required' => false,
                    'sanitize_callback' => function ($value) {
                        return is_string($value) ? trim($value) : '';
                    },
                ],
            ],
        ]);

        register_rest_route('kibsterlp-admin/v1', '/stripe/checkout-status', [
            'methods'  => 'GET',
            'callback' => [$this->controller, 'checkout_status'],
            'permission_callback' => '__return_true',
            'args' => [
                'session_id' => [
                    'required' => true,
                    'sanitize_callback' => 'sanitize_text_field',
                ],
            ],
        ]);

        register_rest_route('kibsterlp-admin/v1', '/stripe/webhook', [
            'methods'  => 'POST',
            'callback' => [$this->controller, 'webhook'],
            'permission_callback' => '__return_true',
        ]);
    }
}
