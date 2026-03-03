<?php
if (!defined('ABSPATH')) exit;

class OrderUserMyOrderEnsureUser
{
    public static function run()
    {
        $current_user = wp_get_current_user();

        if (!$current_user || empty($current_user->ID)) {
            return new \WP_REST_Response([
                'status' => false,
                'message' => 'Unauthorized. Please log in.',
            ], 401);
        }

        return $current_user;
    }
}
