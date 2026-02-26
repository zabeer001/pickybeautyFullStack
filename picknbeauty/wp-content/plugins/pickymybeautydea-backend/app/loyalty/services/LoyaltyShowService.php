<?php
if (!defined('ABSPATH')) {
    exit;
}

class LoyaltyShowService
{
    public static function show(\WP_REST_Request $request)
    {
        global $wpdb;
        $table = $wpdb->prefix . 'kib_loyalty';
        $id = (int) $request['id'];

        $row = $wpdb->get_row($wpdb->prepare("SELECT * FROM {$table} WHERE id = %d", $id), ARRAY_A);

        if (!$row) {
            return new \WP_Error('kib_loyalty_not_found', 'Loyalty rule not found.', ['status' => 404]);
        }

        $row['id'] = (int) $row['id'];
        if (isset($row['min_order'])) $row['min_order'] = (int) $row['min_order'];
        if (isset($row['max_order'])) $row['max_order'] = (int) $row['max_order'];
        if (isset($row['order_complete_percentage'])) $row['order_complete_percentage'] = (int) $row['order_complete_percentage'];
        if (isset($row['discount']))  $row['discount']  = (int) $row['discount'];

        $responseBody = [
            'success' => true,
            'message' => 'Loyalty entry retrieved successfully.',
            'data'    => $row,
        ];

        return new \WP_REST_Response($responseBody, 200);
    }
}
