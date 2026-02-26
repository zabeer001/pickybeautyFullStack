<?php
if (!defined('ABSPATH')) exit;

class CategoryDeleteService
{
    /**
     * DELETE /categories/{id}
     * Delete a category
     */
    public static function delete(\WP_REST_Request $request)
    {
        global $wpdb;
        $table = $wpdb->prefix . 'kib_categories';
        $id    = (int) $request['id'];

        $ok = $wpdb->delete($table, ['id' => $id], ['%d']);
        if (!$ok) {
            return new \WP_Error('kib_category_delete_failed', 'Failed to delete category.', ['status' => 500]);
        }

        return new \WP_REST_Response(['deleted' => (int) $ok], 200);
    }
}
