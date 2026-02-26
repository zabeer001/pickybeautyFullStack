<?php
if (!defined('ABSPATH')) exit;

class CategoryShowService
{
    /**
     * GET /categories/{id}
     * Retrieve single category
     */
    public static function show(\WP_REST_Request $request)
    {
        global $wpdb;
        $table = $wpdb->prefix . 'kib_categories';
        $id    = (int) $request['id'];

        $row = $wpdb->get_row($wpdb->prepare("SELECT * FROM {$table} WHERE id = %d", $id), ARRAY_A);
        if (!$row) {
            return new \WP_Error('kib_category_not_found', 'Category not found.', ['status' => 404]);
        }

        return new \WP_REST_Response($row, 200);
    }
}
