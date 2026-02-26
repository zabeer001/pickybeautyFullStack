<?php
if (!defined('ABSPATH')) {
    exit;
}

require_once __DIR__ . '/CategoryController.php';

class CategoryRoutes
{
    private CategoryController $controller;

    public function __construct()
    {
        $this->controller = new CategoryController();
    }

    public function register_routes(): void
    {
        // ----------------- Categories -----------------

        // GET /wp-json/kibsterlp-admin/v1/categories (public)
        register_rest_route('kibsterlp-admin/v1', '/categories', [
            'methods'  => 'GET',
            'callback' => [$this->controller, 'index'],
            'permission_callback' => '__return_true',
            'args' => [
                'page'     => ['required' => false, 'validate_callback' => 'is_numeric'],
                'per_page' => ['required' => false, 'validate_callback' => 'is_numeric'],
            ],
        ]);

        // POST /wp-json/kibsterlp-admin/v1/categories (create)
        register_rest_route('kibsterlp-admin/v1', '/categories', [
            'methods'  => 'POST',
            'callback' => [$this->controller, 'create'],
            'permission_callback' => function () {
                return current_user_can('manage_options');
            },
            'args' => [
                'title'       => ['required' => true,  'sanitize_callback' => 'sanitize_text_field'],
                'description' => ['required' => false, 'sanitize_callback' => 'sanitize_textarea_field'],
            ],
        ]);

        // GET /wp-json/kibsterlp-admin/v1/categories/{id} (show)
        register_rest_route('kibsterlp-admin/v1', '/categories/(?P<id>\d+)', [
            'methods'  => 'GET',
            'callback' => [$this->controller, 'show'],
            'permission_callback' => '__return_true',
        ]);

        // PUT/PATCH /wp-json/kibsterlp-admin/v1/categories/{id} (update)
        register_rest_route('kibsterlp-admin/v1', '/categories/(?P<id>\d+)', [
            'methods'  => WP_REST_Server::EDITABLE,
            'callback' => [$this->controller, 'update'],
            'permission_callback' => function () {
                return current_user_can('manage_options');
            },
            'args' => [
                'id'          => [
                    'required' => true,
                    'validate_callback' => function ($value, $request, $param) {
                        return is_numeric($value);
                    },
                ],
                'title'       => ['required' => false, 'sanitize_callback' => 'sanitize_text_field'],
                'description' => ['required' => false, 'sanitize_callback' => 'sanitize_textarea_field'],
            ],
        ]);

        // DELETE /wp-json/kibsterlp-admin/v1/categories/{id} (delete)
        register_rest_route('kibsterlp-admin/v1', '/categories/(?P<id>\d+)', [
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
