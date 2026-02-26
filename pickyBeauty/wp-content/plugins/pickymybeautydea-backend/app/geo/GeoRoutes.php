<?php
if (!defined('ABSPATH')) exit;

require_once __DIR__ . '/GeoController.php';

class GeoRoutes
{
    private GeoController $controller;

    public function __construct()
    {
        $this->controller = new GeoController();
    }

    public function register_routes(): void
    {
        register_rest_route('kibsterlp-admin/v1', '/geo/reverse', [
            'methods'  => 'GET',
            'callback' => [$this->controller, 'reverse'],
            'permission_callback' => '__return_true',
            'args' => [
                'lat' => [
                    'required' => true,
                    'sanitize_callback' => 'sanitize_text_field',
                ],
                'lon' => [
                    'required' => true,
                    'sanitize_callback' => 'sanitize_text_field',
                ],
            ],
        ]);
    }
}
