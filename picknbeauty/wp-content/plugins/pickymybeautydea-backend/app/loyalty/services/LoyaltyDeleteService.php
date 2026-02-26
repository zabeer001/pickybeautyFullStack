<?php
if (!defined('ABSPATH')) {
    exit;
}

class LoyaltyDeleteService
{
    public static function delete(\WP_REST_Request $request)
    {
        global $wpdb;
        $table = $wpdb->prefix . 'kib_loyalty';
        $id = (int) $request['id'];

        $ok = $wpdb->delete($table, ['id' => $id], ['%d']);

        if ($ok === false) {
            return new \WP_Error('kib_loyalty_delete_failed', 'Failed to delete loyalty entry.', ['status' => 500]);
        }

        if ($ok === 0) {
            return new \WP_Error('kib_loyalty_not_found', 'Loyalty rule not found for deletion.', ['status' => 404]);
        }

        $responseBody = [
            'success' => true,
            'message' => 'Loyalty entry deleted successfully.',
            'data'    => ['id' => $id],
        ];

        return new \WP_REST_Response($responseBody, 200);
    }
}
