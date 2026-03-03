<?php
if (!defined('ABSPATH')) exit;

class UserLocationStatusService
{
    public static function update(\WP_REST_Request $request)
    {
        $user = wp_get_current_user();
        if ($user->ID === 0) {
            return new \WP_Error(
                'not_logged_in',
                __('User not authenticated', 'kibsterlp'),
                ['status' => 401]
            );
        }

        $status = $request->get_param('status');
        $checked = $request->get_param('checked');

        if (!is_null($checked)) {
            $status = filter_var($checked, FILTER_VALIDATE_BOOLEAN) ? 'active' : 'inactive';
        } else {
            $status = is_null($status) ? null : sanitize_text_field($status);
        }

        if (is_null($status) || !in_array($status, ['active', 'inactive'], true)) {
            return new \WP_Error('invalid_status', __('status must be active or inactive', 'kibsterlp'), ['status' => 400]);
        }

        global $wpdb;
        $table = $wpdb->prefix . 'kib_xy';

        $existing_id = $wpdb->get_var(
            $wpdb->prepare("SELECT id FROM {$table} WHERE user_id = %d", $user->ID)
        );

        if (empty($existing_id)) {
            $inserted = $wpdb->insert(
                $table,
                [
                    'user_id' => $user->ID,
                    'x' => 0,
                    'y' => 0,
                    'radius' => 0,
                    'full_address' => null,
                    'status' => $status,
                ],
                ['%d', '%f', '%f', '%d', '%s', '%s']
            );

            if (!$inserted) {
                return new \WP_Error('db_insert_failed', __('Failed to create location', 'kibsterlp'), ['status' => 500]);
            }
        } else {
            $updated = $wpdb->update(
                $table,
                ['status' => $status],
                ['user_id' => $user->ID],
                ['%s'],
                ['%d']
            );

            if ($updated === false) {
                return new \WP_Error('db_update_failed', __('Failed to update status', 'kibsterlp'), ['status' => 500]);
            }
        }

        return rest_ensure_response([
            'status' => true,
            'message' => __('Status updated successfully', 'kibsterlp'),
            'data' => [
                'status' => $status,
            ],
        ]);
    }
}
