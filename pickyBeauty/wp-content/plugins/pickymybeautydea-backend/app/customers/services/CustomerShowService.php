<?php

if (!defined('ABSPATH')) exit;

class CustomerShowService
{
    /**
     * GET /customers/{id}
     * Retrieve single customer
     */
    public static function show(\WP_REST_Request $request)
    {
        global $wpdb;
        $table = $wpdb->prefix . 'kib_customers';
        $id    = (int) $request['id'];

        $row = $wpdb->get_row($wpdb->prepare("SELECT * FROM {$table} WHERE id = %d", $id), ARRAY_A);
        if (!$row) {
            return new \WP_Error('kib_customer_not_found', 'Customer not found.', ['status' => 404]);
        }

        return new \WP_REST_Response($row, 200);
    }
}
