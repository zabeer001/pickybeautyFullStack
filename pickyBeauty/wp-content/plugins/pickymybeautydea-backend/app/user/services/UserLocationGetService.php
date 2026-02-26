<?php
if (!defined('ABSPATH')) exit;

class UserLocationGetService
{
    public static function get(\WP_REST_Request $request)
    {
        $user = wp_get_current_user();
        if ($user->ID === 0) {
            return new \WP_Error(
                'not_logged_in',
                __('User not authenticated', 'kibsterlp'),
                ['status' => 401]
            );
        }

        global $wpdb;
        $table = $wpdb->prefix . 'kib_xy';

        $row = $wpdb->get_row(
            $wpdb->prepare("SELECT user_id, x, y, radius, full_address, status FROM {$table} WHERE user_id = %d", $user->ID),
            ARRAY_A
        );

        $data = null;
        if (!empty($row)) {
            $data = [
                'user_id' => (int) $row['user_id'],
                'x' => isset($row['x']) ? (int) $row['x'] : 0,
                'y' => isset($row['y']) ? (int) $row['y'] : 0,
                'radius' => isset($row['radius']) ? (int) $row['radius'] : 0,
                'full_address' => $row['full_address'],
                'status' => $row['status'],
            ];
        }

        return rest_ensure_response([
            'status' => true,
            'message' => __('Location retrieved successfully', 'kibsterlp'),
            'data' => $data,
        ]);
    }
}
