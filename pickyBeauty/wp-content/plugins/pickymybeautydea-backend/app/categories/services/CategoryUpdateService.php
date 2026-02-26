<?php
if (!defined('ABSPATH')) exit;

class CategoryUpdateService
{
    /**
     * PATCH /categories/{id}
     * Update existing category
     */
    public static function update(\WP_REST_Request $request)
    {
        global $wpdb;
        $table = $wpdb->prefix . 'kib_categories';
        $id    = (int) $request['id'];

        $data  = [];
        $types = [];

        $map = [
            'title'       => '%s',
            'description' => '%s',
        ];

        foreach ($map as $key => $type) {
            if (null !== $request->get_param($key)) {
                $val = $request->get_param($key);
                $val = $key === 'description' ? sanitize_textarea_field($val) : sanitize_text_field($val);
                $data[$key] = $val;
                $types[] = $type;
            }
        }

        if (empty($data)) {
            return new \WP_Error('kib_category_no_changes', 'No fields to update.', ['status' => 400]);
        }

        // Always update timestamp
        $data['updated_at'] = current_time('mysql');
        $types[] = '%s';

        $ok = $wpdb->update($table, $data, ['id' => $id], $types, ['%d']);
        if ($ok === false) {
            return new \WP_Error('kib_category_update_failed', 'Failed to update category.', ['status' => 500]);
        }

        return new \WP_REST_Response(['updated' => (int) $ok], 200);
    }
}
