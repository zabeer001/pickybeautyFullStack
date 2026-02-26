<?php
if (!defined('ABSPATH')) {
    exit;
}

require_once __DIR__ . '/OrderController.php';

class OrderRoutes
{
    private OrderController $controller;

    public function __construct()
    {
        $this->controller = new OrderController();
    }

    public function register_routes(): void
    {
        // ----------------- Orders -----------------

        // GET /orders
        register_rest_route('kibsterlp-admin/v1', '/orders', [
            'methods'  => 'GET',
            'callback' => [$this->controller, 'index'],
            'permission_callback' => function (\WP_REST_Request $request) {
                return current_user_can('manage_options');
            },
            'args' => [
                'page' => [
                    'required' => false,
                    'validate_callback' => function ($value, $request, $param) {
                        return is_null($value) || is_numeric($value);
                    },
                    'sanitize_callback' => 'absint',
                ],
                'per_page' => [
                    'required' => false,
                    'validate_callback' => function ($value, $request, $param) {
                        return is_null($value) || is_numeric($value);
                    },
                    'sanitize_callback' => 'absint',
                ],
            ],
        ]);

        register_rest_route('kibsterlp-admin/v1', '/vendor-my-orders', [
            'methods'  => 'GET',
            'callback' => [$this->controller, 'myOrder'],
            'permission_callback' => function (\WP_REST_Request $request) {
                $user = wp_get_current_user();
                return in_array('vendor', (array) $user->roles, true);
            },
            'args' => [
                'page' => [
                    'required' => false,
                    'validate_callback' => function ($value, $request, $param) {
                        return is_null($value) || is_numeric($value);
                    },
                    'sanitize_callback' => 'absint',
                ],
                'per_page' => [
                    'required' => false,
                    'validate_callback' => function ($value, $request, $param) {
                        return is_null($value) || is_numeric($value);
                    },
                    'sanitize_callback' => 'absint',
                ],
            ],
        ]);

        // My Orders for regular users (subscribers/customers)
        register_rest_route('kibsterlp-admin/v1', '/my-orders', [
            'methods'  => 'GET',
            'callback' => [$this->controller, 'userMyOrder'],
            'permission_callback' => function (\WP_REST_Request $request) {

                if (!is_user_logged_in()) {
                    return new WP_Error(
                        'rest_forbidden',
                        __('You must be logged in.'),
                        ['status' => 401]
                    );
                }

                $user = wp_get_current_user();

                if (in_array('subscriber', (array) $user->roles, true)) {
                    return true;
                }

                return new WP_Error(
                    'rest_forbidden_role',
                    __('Only subscribers can access this endpoint.'),
                    ['status' => 403]
                );
            },
            'args' => [
                'page' => [
                    'required' => false,
                    'validate_callback' => function ($value) {
                        return is_null($value) || is_numeric($value);
                    },
                    'sanitize_callback' => 'absint',
                ],
                'per_page' => [
                    'required' => false,
                    'validate_callback' => function ($value) {
                        return is_null($value) || is_numeric($value);
                    },
                    'sanitize_callback' => 'absint',
                ],
            ],
        ]);
        
        register_rest_route('kibsterlp-admin/v1', '/orders/(?P<uniq_id>[A-Za-z0-9\\-]+)', [
            'methods'  => 'GET',
            'callback' => [$this->controller, 'show'],
            'permission_callback' => function () {
                return true;
            },
        ]);

        register_rest_route('kibsterlp-admin/v1', '/accept-order/(?P<order_unique_id>[a-zA-Z0-9-_]+)', [
            'methods'  => 'PUT',
            'callback' => [$this->controller, 'acceptOrder'],
            'permission_callback' => function () {
                $user = wp_get_current_user();
                return in_array('vendor', (array) $user->roles, true);
            },
            'args' => [
                'order_unique_id' => [
                    'required' => true,
                    'sanitize_callback' => 'sanitize_text_field',
                ],
                'sharing_status' => [
                    'required' => true,
                    'validate_callback' => function ($value) {
                        $allowed_statuses = ['Accepted'];
                        return in_array($value, $allowed_statuses, true);
                    },
                    'sanitize_callback' => 'sanitize_text_field',
                ],
            ],
        ]);

        register_rest_route('kibsterlp-admin/v1', '/admin-payment-update/(?P<id>\d+)', [
            'methods'  => 'PUT',
            'callback' => [$this->controller, 'update_payment_status'],
            'permission_callback' => '__return_true',
            'args' => [
                'id' => [
                    'required' => true,
                    'validate_callback' => function ($value) {
                        return is_numeric($value);
                    },
                    'sanitize_callback' => 'absint',
                ],
                'payment_status' => [
                    'required' => true,
                    'validate_callback' => function ($value) {
                        $allowed_statuses = ['paid', 'Paid'];
                        return in_array($value, $allowed_statuses, true);
                    },
                    'sanitize_callback' => 'sanitize_text_field',
                ],
            ],
        ]);

        register_rest_route('kibsterlp-admin/v1', '/orders', [
            'methods'  => 'POST',
            'callback' => [$this->controller, 'create'],
            'permission_callback' => '__return_true',
            'args' => [
                'category_id' => [
                    'required' => false,
                    'validate_callback' => '__return_true', // skip validation
                ],
                'vendor_id' => [
                    'required' => false,
                    'validate_callback' => '__return_true', // skip validation
                ],
                'budget' => [
                    'required' => true,
                    'validate_callback' => '__return_true', // skip validation
                ],

                'shipping' => [
                    'required' => true,
                    'validate_callback' => '__return_true', // skip validation
                ],
                'order_title' => [
                    'required' => false,
                    'sanitize_callback' => function ($value, $request, $param) {
                        return sanitize_text_field($value);
                    },
                ],
                'sharing_status' => [
                    'required' => false,
                    'sanitize_callback' => function ($value, $request, $param) {
                        return sanitize_text_field($value);
                    },
                ],
                'order_details' => [
                    'required' => false,
                    'sanitize_callback' => function ($value, $request, $param) {
                        return sanitize_text_field($value);
                    },
                ],
                'payment_method' => [
                    'required' => false,
                    'sanitize_callback' => function ($value, $request, $param) {
                        return sanitize_text_field($value);
                    },
                ],
            ],
        ]);

        register_rest_route('kibsterlp-admin/v1', '/order-discount', [
            'methods'  => 'POST',
            'callback' => [$this->controller, 'orderDiscount'],
            'permission_callback' => '__return_true',
            'args' => [
                'email' => [
                    'required' => true,
                    'validate_callback' => fn($value) => is_email($value),
                    'sanitize_callback' => 'sanitize_email',
                ],
                'budget' => [
                    'required' => true,
                    'validate_callback' => fn($value) => is_numeric($value) && $value >= 0,
                    'sanitize_callback' => fn($value) => (float) $value,
                ],
            ],
        ]);

        register_rest_route('kibsterlp-admin/v1', '/orders/(?P<id>\d+)', [
            'methods'  => WP_REST_Server::EDITABLE,
            'callback' => [$this->controller, 'update'],
            'permission_callback' => function (\WP_REST_Request $request) {
                return current_user_can('manage_options');
            },
            'args' => [
                'id' => [
                    'required' => true,
                    'validate_callback' => function ($value, $request, $param) {
                        return is_numeric($value);
                    },
                    'sanitize_callback' => function ($value) {
                        return absint($value);
                    },
                ],
                'vendor_id' => [
                    'required' => false,
                    'validate_callback' => function ($value, $request, $param) {
                        return is_null($value) || is_numeric($value);
                    },
                    'sanitize_callback' => function ($value) {
                        return absint($value);
                    },
                ],
                'price' => [
                    'required' => false,
                    'validate_callback' => function ($value, $request, $param) {
                        return is_null($value) || is_numeric($value);
                    },
                    'sanitize_callback' => function ($value) {
                        return floatval($value);
                    },
                ],
                'shipping_id' => [
                    'required' => false,
                    'validate_callback' => function ($value, $request, $param) {
                        return is_null($value) || is_numeric($value);
                    },
                    'sanitize_callback' => function ($value) {
                        return absint($value);
                    },
                ],
                'shipping' => [
                    'required' => false,
                    'validate_callback' => function ($value, $request, $param) {
                        return is_null($value) || is_array($value);
                    },
                ],
                'budget' => [
                    'required' => false,
                    'validate_callback' => function ($value, $request, $param) {
                        return is_null($value) || is_numeric($value);
                    },
                    'sanitize_callback' => function ($value) {
                        return floatval($value);
                    },
                ],
                'order_title' => [
                    'required' => false,
                    'sanitize_callback' => function ($value) {
                        return sanitize_text_field($value);
                    },
                ],
                'sharing_status' => [
                    'required' => false,
                    'sanitize_callback' => function ($value) {
                        return sanitize_text_field($value);
                    },
                ],
            ],
        ]);

        register_rest_route('kibsterlp-admin/v1', '/orders/(?P<id>\d+)', [
            'methods'  => 'DELETE',
            'callback' => [$this->controller, 'delete'],
            'permission_callback' => '__return_true',
            'args' => [
                'id' => [
                    'required' => true,
                    'validate_callback' => function ($value, $request, $param) {
                        return is_numeric($value);
                    },
                    'sanitize_callback' => function ($value) {
                        return absint($value);
                    },
                ],
            ],
        ]);
    }
}
