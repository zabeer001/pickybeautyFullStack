<?php

if (!defined('ABSPATH')) exit;

class CustomerCreateService
{
    /**
     * POST /customers
     * Create new customer
     */
    public static function create(\WP_REST_Request $request)
    {

        global $wpdb;
        $table = $wpdb->prefix . 'kib_customers';

        $name = sanitize_text_field($request->get_param('name'));
        $email = sanitize_email($request->get_param('email'));
        $phone = sanitize_text_field($request->get_param('phone'));
        $order_complete_count = (int) $request->get_param('order_complete_count');
        $order_cancelled_count = (int) $request->get_param('order_cancelled_count');
        $order_complete_percentage = (int) $request->get_param('order_complete_percentage');

        if (empty($email)) {
            return new \WP_Error('kib_customer_email_required', 'Customer email is required.', ['status' => 400]);
        }

        if (!is_email($email)) {
            return new \WP_Error('kib_customer_email_invalid', 'Customer email is not valid.', ['status' => 400]);
        }

        $ok = $wpdb->insert($table, [
            'name'        => $name,
            'email'       => $email,
            'phone'       => $phone,
            'order_complete_count' => $order_complete_count,
            'order_cancelled_count' => $order_cancelled_count,
            'order_complete_percentage' => $order_complete_percentage,
            'created_at'  => current_time('mysql'),
            'updated_at'  => current_time('mysql'),
        ], ['%s', '%s', '%s', '%d', '%d', '%d', '%s', '%s']);

        if (!$ok) {
            return new \WP_Error('kib_customer_create_failed', 'Failed to create customer.', ['status' => 500]);
        }

        $id = (int) $wpdb->insert_id;
        return new \WP_REST_Response([
            'id'    => $id,
            'email' => $email,
        ], 201);
    }
}
