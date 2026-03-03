<?php
if (!defined('ABSPATH')) exit;

class UserLocationService
{
    public static function save(
        \WP_REST_Request $request
    ) {
        $user = wp_get_current_user();
        if ($user->ID === 0) {
            return new \WP_Error(
                'not_logged_in',
                __('User not authenticated', 'kibsterlp'),
                ['status' => 401]
            );
        }

        $x = $request->get_param('x');
        $y = $request->get_param('y');
        $radius = $request->get_param('radius');
        $full_address = $request->get_param('full_address');
        $status = $request->get_param('status');

        $fields = [];
        $formats = [];

        if (!is_null($x)) {
            if (!is_numeric($x)) {
                return new \WP_Error('invalid_x', __('x must be numeric', 'kibsterlp'), ['status' => 400]);
            }
            $fields['x'] = round((float) $x, 6);
            $formats[] = '%f';
        }

        if (!is_null($y)) {
            if (!is_numeric($y)) {
                return new \WP_Error('invalid_y', __('y must be numeric', 'kibsterlp'), ['status' => 400]);
            }
            $fields['y'] = round((float) $y, 6);
            $formats[] = '%f';
        }

        if (!is_null($radius)) {
            if (!is_numeric($radius)) {
                return new \WP_Error('invalid_radius', __('radius must be numeric', 'kibsterlp'), ['status' => 400]);
            }
            $fields['radius'] = (int) $radius;
            $formats[] = '%d';
        }

        if (!is_null($full_address)) {
            $fields['full_address'] = sanitize_textarea_field($full_address);
            $formats[] = '%s';
        }

        if (!is_null($status)) {
            $status = sanitize_text_field($status);
            if (!in_array($status, ['active', 'inactive'], true)) {
                return new \WP_Error('invalid_status', __('status must be active or inactive', 'kibsterlp'), ['status' => 400]);
            }
            $fields['status'] = $status;
            $formats[] = '%s';
        }

        if (empty($fields)) {
            return new \WP_Error('missing_fields', __('No fields to update', 'kibsterlp'), ['status' => 400]);
        }

        global $wpdb;
        $table = $wpdb->prefix . 'kib_xy';

        $existing_id = $wpdb->get_var(
            $wpdb->prepare("SELECT id FROM {$table} WHERE user_id = %d", $user->ID)
        );

        if (empty($existing_id)) {
            if (!isset($fields['status'])) {
                $fields['status'] = 'active';
                $formats[] = '%s';
            }

            $insert_data = ['user_id' => $user->ID] + $fields;
            $insert_formats = array_merge(['%d'], $formats);

            $inserted = $wpdb->insert($table, $insert_data, $insert_formats);
            if (!$inserted) {
                return new \WP_Error('db_insert_failed', __('Failed to create location', 'kibsterlp'), ['status' => 500]);
            }
        } else {
            $updated = $wpdb->update($table, $fields, ['user_id' => $user->ID], $formats, ['%d']);
            if ($updated === false) {
                return new \WP_Error('db_update_failed', __('Failed to update location', 'kibsterlp'), ['status' => 500]);
            }
        }

        return rest_ensure_response([
            'status' => true,
            'message' => __('Location saved successfully', 'kibsterlp'),
        ]);
    }
}
