<?php
if (!defined('ABSPATH')) exit;

class OrderCreateFindOrCreateCustomer
{
    public static function run(?array $shipping): ?int
    {
        global $wpdb;

        if (empty($shipping['email']) || !is_email($shipping['email'])) {
            return null;
        }

        $customers_table = $wpdb->prefix . 'kib_customers';
        $email = sanitize_email($shipping['email']);

        $existing_customer_id = $wpdb->get_var(
            $wpdb->prepare("SELECT id FROM {$customers_table} WHERE email = %s", $email)
        );

        if ($existing_customer_id) {
            return (int) $existing_customer_id;
        }

        $ok_customer = $wpdb->insert(
            $customers_table,
            [
                'name' => isset($shipping['name']) ? sanitize_text_field($shipping['name']) : '',
                'email' => $email,
                'phone' => isset($shipping['phone']) ? sanitize_text_field($shipping['phone']) : null,
                'created_at' => current_time('mysql'),
                'updated_at' => current_time('mysql'),
            ],
            ['%s', '%s', '%s', '%s', '%s']
        );

        if (!$ok_customer) {
            return null;
        }

        return (int) $wpdb->insert_id;
    }
}
