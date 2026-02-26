<?php
if (!defined('ABSPATH')) {
    exit;
}

require_once __DIR__ . '/LoyaltyController.php';

class LoyaltyRoutes
{
    private LoyaltyController $controller;

    public function __construct()
    {
        $this->controller = new LoyaltyController();
    }

    public function register_routes(): void
    {
        $permission = function () {
            return current_user_can('manage_options');
        };

        register_rest_route('kibsterlp-admin/v1', '/loyalty', [
            'methods'  => 'GET',
            'callback' => [$this->controller, 'index'],
            'permission_callback' => $permission,
        ]);

        register_rest_route('kibsterlp-admin/v1', '/loyalty', [
            'methods'  => 'POST',
            'callback' => [$this->controller, 'create'],
            'permission_callback' => $permission,
            'args' => [
                'min_order' => [
                    'required' => true,
                    'validate_callback' => '__return_true', // skip validation
                ],
                'max_order' => [
                    'required' => true,
                    'validate_callback' => '__return_true', // skip validation
                ],
                'order_complete_percentage'  => [
                    'required' => true,
                    'validate_callback' => '__return_true', // skip validation
                ],
                'discount'  => [
                    'required' => true,
                    'validate_callback' => '__return_true', // skip validation
                ],
                'status'    => [
                    'required' => false,
                    'sanitize_callback' => 'sanitize_text_field',
                ],
            ],
        ]);

        register_rest_route('kibsterlp-admin/v1', '/loyalty/(?P<id>\d+)', [
            'methods'  => 'GET',
            'callback' => [$this->controller, 'show'],
            'permission_callback' => $permission,
        ]);

        // Original method for UPDATE/EDIT
      // --- Route: PUT/PATCH /loyalty/(?P<id>\d+) (Update) ---
register_rest_route('kibsterlp-admin/v1', '/loyalty/(?P<id>\d+)', [
    // Recommended change: Use explicit methods PUT/PATCH to avoid conflict with POST/create
    'methods'  => ['PUT', 'PATCH'], 
    'callback' => [$this->controller, 'update'],
    'permission_callback' => $permission,
    'args' => [
        // FIX: Replaced 'is_numeric' string with a wrapper function
        'id'        => [
            'required' => true,
            'description' => 'The ID of the loyalty rule to update.',
            'validate_callback' => function( $value, $request, $param ) {
                // The anonymous function accepts the 3 required arguments
                // but only passes the actual $value to is_numeric()
                return is_numeric( $value );
            },
        ],
        // Input fields: Removed unnecessary validate/sanitize callbacks as handling is centralized
        // in LoyaltyController::map_payload(), and set to required => false.
        'min_order' => [
            'required' => false,
            'description' => 'Minimum order amount for the loyalty rule.'
        ],
        'max_order' => [
            'required' => false,
            'description' => 'Maximum order amount for the loyalty rule.'
        ],
        'order_complete_percentage'  => [
            'required' => false,
            'description' => 'Order completion percentage threshold for the loyalty rule.'
        ],
        'discount'  => [
            'required' => false,
            'description' => 'Discount percentage (integer) to apply.'
        ],
        'status'    => [
            'required' => false,
            'description' => 'Status of the loyalty rule (e.g., active, inactive).'
        ],
    ],
]);

        register_rest_route('kibsterlp-admin/v1', '/loyalty/(?P<id>\d+)', [
            'methods'  => 'DELETE',
            'callback' => [$this->controller, 'delete'],
            'permission_callback' => $permission,
            'args' => [
                'id' => ['required' => true, 'validate_callback' => 'is_numeric'],
            ],
        ]);
    }
}
