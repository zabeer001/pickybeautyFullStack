<?php
if (!defined('ABSPATH')) exit;

class OrderCreateInsertShipping
{
    public static function run(array $shipping)
    {
        global $wpdb;

        $ok = $wpdb->insert(
            $wpdb->prefix . 'kib_shipping_addresses',
            [
                'email' => $shipping['email'],
                'phone' => $shipping['phone'],
                'name' => $shipping['name'],
                'country' => $shipping['country'],
                'city' => $shipping['city'],
                'district' => $shipping['district'],
                'zip_code' => $shipping['zip_code'],
                'created_at' => current_time('mysql'),
                'updated_at' => current_time('mysql'),
            ],
            ['%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s']
        );

        if (!$ok) {
            return new \WP_Error('kib_shipping_create_failed', 'Failed to create shipping address', ['status' => 500]);
        }

        return (int) $wpdb->insert_id;
    }
}
