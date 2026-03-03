<?php
if (!defined('ABSPATH')) exit;

class OrderDiscountFindCustomer
{
    public static function run($wpdb, string $email): ?array
    {
        $customer = $wpdb->get_row(
            $wpdb->prepare("SELECT * FROM {$wpdb->prefix}kib_customers WHERE email = %s", $email),
            ARRAY_A
        );

        return is_array($customer) ? $customer : null;
    }
}
