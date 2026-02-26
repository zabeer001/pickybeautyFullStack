<?php

if (!defined('ABSPATH')) exit;

class CustomerUpdateService
{
    /**
     * PATCH /customers/{id}
     * Update existing customer
     */
    public static function update(\WP_REST_Request $request)
    {
        global $wpdb;

        $table = $wpdb->prefix . 'kib_customers';
        $id    = (int) $request->get_param('id'); // safer than $request['id']

        if ($id <= 0) {
            return new \WP_Error('kib_customer_id_invalid', 'Customer id is invalid.', ['status' => 400]);
        }

        // Optional but recommended: ensure customer exists
        $exists = $wpdb->get_var(
            $wpdb->prepare("SELECT id FROM {$table} WHERE id = %d", $id)
        );
        if (!$exists) {
            return new \WP_Error('kib_customer_not_found', 'Customer not found.', ['status' => 404]);
        }

        $data  = [];
        $types = [];

        $map = [
            'name'                     => '%s',
            'email'                    => '%s',
            'phone'                    => '%s',
            'order_complete_count'     => '%d',
            'order_cancelled_count'    => '%d',
            'order_complete_percentage'=> '%d',
        ];

        foreach ($map as $key => $type) {
            $raw = $request->get_param($key);
            if ($raw === null) continue;

            $raw = wp_unslash($raw);

            if ($key === 'email') {
                $val = sanitize_email($raw);
                if ($val === '' || !is_email($val)) {
                    return new \WP_Error('kib_customer_email_invalid', 'Customer email is not valid.', ['status' => 400]);
                }

                // Optional but recommended: unique email (except self)
                $email_taken = $wpdb->get_var(
                    $wpdb->prepare("SELECT id FROM {$table} WHERE email = %s AND id <> %d", $val, $id)
                );
                if ($email_taken) {
                    return new \WP_Error('kib_customer_email_exists', 'Customer with this email already exists.', ['status' => 409]);
                }

            } elseif (in_array($key, ['order_complete_count', 'order_cancelled_count', 'order_complete_percentage'], true)) {
                $val = (int) $raw;

                if ($val < 0) {
                    return new \WP_Error('kib_customer_count_invalid', "{$key} must be 0 or greater.", ['status' => 400]);
                }

                if ($key === 'order_complete_percentage' && $val > 100) {
                    return new \WP_Error('kib_customer_percentage_invalid', 'order_complete_percentage must be between 0 and 100.', ['status' => 400]);
                }

            } else {
                $val = sanitize_text_field($raw);
            }

            $data[$key] = $val;
            $types[] = $type;
        }

        if (empty($data)) {
            return new \WP_Error('kib_customer_no_changes', 'No fields to update.', ['status' => 400]);
        }

        // Always update timestamp
        $data['updated_at'] = current_time('mysql');
        $types[] = '%s';

        $ok = $wpdb->update($table, $data, ['id' => $id], $types, ['%d']);

        if ($ok === false) {
            return new \WP_Error(
                'kib_customer_update_failed',
                $wpdb->last_error ?: 'Failed to update customer.',
                ['status' => 500]
            );
        }

        // $ok can be 0 if same values were sent
        return new \WP_REST_Response(['id' => $id, 'updated' => (int) $ok], 200);
    }
}
