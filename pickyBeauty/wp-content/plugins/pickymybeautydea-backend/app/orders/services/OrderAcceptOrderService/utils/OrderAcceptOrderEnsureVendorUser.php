<?php
if (!defined('ABSPATH')) exit;

class OrderAcceptOrderEnsureVendorUser
{
    public static function run()
    {
        $current_user = wp_get_current_user();

        if (!$current_user || !in_array('vendor', (array) $current_user->roles, true)) {
            return new \WP_REST_Response([
                'status' => false,
                'message' => 'Unauthorized. Only vendors can accept orders.',
            ], 403);
        }

        return $current_user;
    }
}
