<?php
if (!defined('ABSPATH')) exit;

class CategoryCreateService
{
    /**
     * POST /categories
     * Create new category
     */
    public static function create(\WP_REST_Request $request)
    {
        global $wpdb;
        $table = $wpdb->prefix . 'kib_categories';

        $title       = sanitize_text_field($request->get_param('title'));
        $description = sanitize_textarea_field($request->get_param('description'));

        if (empty($title)) {
            return new \WP_Error('kib_category_title_required', 'Category title is required.', ['status' => 400]);
        }

        $ok = $wpdb->insert($table, [
            'title'       => $title,
            'description' => $description,
            'created_at'  => current_time('mysql'),
            'updated_at'  => current_time('mysql'),
        ], ['%s', '%s', '%s', '%s']);

        if (!$ok) {
            return new \WP_Error('kib_category_create_failed', 'Failed to create category.', ['status' => 500]);
        }

        $id = (int) $wpdb->insert_id;
        return new \WP_REST_Response([
            'id'    => $id,
            'title' => $title,
        ], 201);
    }
}
