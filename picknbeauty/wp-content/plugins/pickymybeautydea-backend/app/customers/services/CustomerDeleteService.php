<?php

if (!defined('ABSPATH')) exit;

class CustomerDeleteService
{
    /**
     * DELETE /customers/{id}
     * Delete a customer
     */
    public static function delete(\WP_REST_Request $request)
    {
        global $wpdb;
        $table = $wpdb->prefix . 'kib_customers';
        $id    = (int) $request['id'];

        $ok = $wpdb->delete($table, ['id' => $id], ['%d']);
        if (!$ok) {
            return new \WP_Error('kib_customer_delete_failed', 'Failed to delete customer.', ['status' => 500]);
        }

        return new \WP_REST_Response(['deleted' => (int) $ok], 200);
    }
}
