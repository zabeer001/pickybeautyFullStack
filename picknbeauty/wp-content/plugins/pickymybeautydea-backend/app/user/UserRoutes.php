<?php
if (!defined('ABSPATH')) {
    exit;
}

require_once __DIR__ . '/UserController.php';

class UserRoutes
{
    private UserController $controller;

    public function __construct()
    {
        $this->controller = new UserController();
    }

    public function register_routes(): void
    {
        register_rest_route('kibsterlp-admin/v1', '/users', [
            'methods'  => 'GET',
            'callback' => [$this->controller, 'index'],
            'permission_callback' => function (\WP_REST_Request $request) {
                return current_user_can('manage_options');
            },
            'args' => [
                'page' => [
                    'required' => false,
                    'validate_callback' => fn ($value, $request, $param) => is_null($value) || is_numeric($value),
                    'sanitize_callback' => 'absint',
                ],
                'per_page' => [
                    'required' => false,
                    'validate_callback' => fn ($value, $request, $param) => is_null($value) || is_numeric($value),
                    'sanitize_callback' => 'absint',
                ],
            ],
        ]);

        register_rest_route('kibsterlp-admin/v1', '/user/location', [
            'methods'  => 'GET',
            'callback' => [$this->controller, 'get_location'],
            'permission_callback' => function () {
                return is_user_logged_in();
            },
        ]);

        register_rest_route('kibsterlp-admin/v1', '/user/location', [
            'methods'  => ['POST', 'PUT', 'PATCH'],
            'callback' => [$this->controller, 'save_location'],
            'permission_callback' => function () {
                return is_user_logged_in();
            },
            'args' => [
                'x' => [
                    'required' => false,
                    'validate_callback' => function ($value) {
                        return is_null($value) || is_numeric($value);
                    },
                ],
                'y' => [
                    'required' => false,
                    'validate_callback' => function ($value) {
                        return is_null($value) || is_numeric($value);
                    },
                ],
                'radius' => [
                    'required' => false,
                    'validate_callback' => function ($value) {
                        return is_null($value) || is_numeric($value);
                    },
                ],
                'full_address' => [
                    'required' => false,
                    'sanitize_callback' => 'sanitize_textarea_field',
                ],
                'status' => [
                    'required' => false,
                    'sanitize_callback' => 'sanitize_text_field',
                ],
            ],
        ]);

        register_rest_route('kibsterlp-admin/v1', '/user/location/status', [
            'methods'  => ['POST', 'PUT', 'PATCH'],
            'callback' => [$this->controller, 'update_location_status'],
            'permission_callback' => function () {
                return is_user_logged_in();
            },
            'args' => [
                'status' => [
                    'required' => false,
                    'sanitize_callback' => 'sanitize_text_field',
                ],
                'checked' => [
                    'required' => false,
                    'validate_callback' => '__return_true',
                ],
            ],
        ]);
    }
}
