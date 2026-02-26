<?php
if (!defined('ABSPATH')) {
    exit;
}

require_once __DIR__ . '/CustomerController.php';

class CustomerRoutes
{
    private CustomerController $controller;

    public function __construct()
    {
        $this->controller = new CustomerController();
    }

    public function register_routes(): void
    {
        // ----------------- Customers -----------------

        // GET /wp-json/kibsterlp-admin/v1/customers (admin only)
        register_rest_route('kibsterlp-admin/v1', '/customers', [
            'methods'  => 'GET',
            'callback' => [$this->controller, 'index'],
            'permission_callback' => function () {
                return current_user_can('manage_options');
            },
            'args' => [
                'page'     => ['required' => false],
                'per_page' => ['required' => false],
            ],
        ]);

        // POST /wp-json/kibsterlp-admin/v1/customers (create)
        register_rest_route('kibsterlp-admin/v1', '/customers', [
            'methods'  => 'POST',
            'callback' => [$this->controller, 'create'],
            'permission_callback' => function () {
                return current_user_can('manage_options');
            },
            'args' => [
                'name' => [
                    'required' => false,
                    'sanitize_callback' => 'sanitize_text_field',
                ],

                'email' => [
                    'required' => true,
                    'sanitize_callback' => 'sanitize_email',
                ],

                'phone' => [
                    'required' => false,
                    'sanitize_callback' => 'sanitize_text_field',
                ],

                // skip numeric validation (form-data comes as string)
                'order_complete_count' => [
                    'required' => false,
                    'validate_callback' => '__return_true',
                ],

                // required but no validation
                'order_cancelled_count' => [
                    'required' => true,
                    'validate_callback' => '__return_true',
                ],

                // percentage still validated
                'order_complete_percentage' => [
                    'required' => false,
                    'validate_callback' => function ($value) {
                        return is_numeric($value)
                            && (int) $value >= 0
                            && (int) $value <= 100;
                    },
                ],
            ],
        ]);

        // GET /wp-json/kibsterlp-admin/v1/customers/{id} (show)
        register_rest_route('kibsterlp-admin/v1', '/customers/(?P<id>\d+)', [
            'methods'  => 'GET',
            'callback' => [$this->controller, 'show'],
            'permission_callback' => function () {
                return current_user_can('manage_options');
            },
        ]);

        // PUT/PATCH /wp-json/kibsterlp-admin/v1/customers/{id} (update)
      register_rest_route('kibsterlp-admin/v1', '/customers/(?P<id>\d+)', [
    'methods'  => ['PUT', 'PATCH'],
    'callback' => [$this->controller, 'update'],
    'permission_callback' => function () {
        return current_user_can('manage_options');
    },
    'args' => [
        'id' => [
            'required' => true,
            'validate_callback' => function ($value) {
                return is_numeric($value);
            },
        ],

        'name'  => ['required' => false, 'sanitize_callback' => 'sanitize_text_field'],
        'email' => ['required' => false, 'sanitize_callback' => 'sanitize_email'],
        'phone' => ['required' => false, 'sanitize_callback' => 'sanitize_text_field'],

        // skip validation (form-data comes as string)
        'order_complete_count' => [
            'required' => false,
            'validate_callback' => '__return_true',
        ],
        'order_cancelled_count' => [
            'required' => false,
            'validate_callback' => '__return_true',
        ],

        // keep percentage validation
        'order_complete_percentage' => [
            'required' => false,
            'validate_callback' => function ($value) {
                return is_numeric($value) && (int) $value >= 0 && (int) $value <= 100;
            },
        ],
    ],
]);


        // DELETE /wp-json/kibsterlp-admin/v1/customers/{id} (delete)
        register_rest_route('kibsterlp-admin/v1', '/customers/(?P<id>\d+)', [
            'methods'  => 'DELETE',
            'callback' => [$this->controller, 'delete'],
            'permission_callback' => function () {
                return current_user_can('manage_options');
            },
            'args' => [
                'id' => [
                    'required' => true,
                    'validate_callback' => function ($value, $request, $param) {
                        return is_numeric($value);
                    },
                ],
            ],
        ]);
    }
}
