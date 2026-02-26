<?php
if (!defined('ABSPATH')) {
    exit;
}

class LoyaltyIndexService
{
    public static function index(\WP_REST_Request $request)
    {
        global $wpdb;
        $table = $wpdb->prefix . 'kib_loyalty';

        $rows = $wpdb->get_results("SELECT * FROM {$table} ORDER BY id ASC", ARRAY_A);

        $rows = array_map(function ($row) {
            $row['id'] = (int) $row['id'];
            if (isset($row['min_order'])) $row['min_order'] = (int) $row['min_order'];
            if (isset($row['max_order'])) $row['max_order'] = (int) $row['max_order'];
            if (isset($row['order_complete_percentage'])) $row['order_complete_percentage'] = (int) $row['order_complete_percentage'];
            if (isset($row['discount']))  $row['discount']  = (int) $row['discount'];
            return $row;
        }, $rows);

        $responseBody = [
            'success' => true,
            'message' => 'Loyalty entries retrieved successfully.',
            'data'    => $rows,
        ];

        return new \WP_REST_Response($responseBody, 200);
    }
}
