<?php
if (!defined('ABSPATH')) {
    exit;
}

class LoyaltyUpdateService
{
    public static function update(\WP_REST_Request $request)
    {
        global $wpdb;
        $table = $wpdb->prefix . 'kib_loyalty';
        $id = (int) $request['id'];

        $payload = self::map_payload($request);

        if (empty($payload)) {
            return new \WP_Error('kib_loyalty_update_empty', 'Nothing to update.', ['status' => 400]);
        }

        $payload['updated_at'] = current_time('mysql');

        $row_check = $wpdb->get_var($wpdb->prepare("SELECT id FROM {$table} WHERE id = %d", $id));
        if (!$row_check) {
            return new \WP_Error('kib_loyalty_not_found', 'Loyalty rule not found for update.', ['status' => 404]);
        }

        $ok = $wpdb->update($table, $payload, ['id' => $id], ['%d', '%d', '%d', '%d', '%s'], ['%d']);

        if ($ok === false) {
            return new \WP_Error('kib_loyalty_update_failed', 'Failed to update loyalty entry.', ['status' => 500]);
        }

        $row = $wpdb->get_row($wpdb->prepare("SELECT * FROM {$table} WHERE id = %d", $id), ARRAY_A);

        $row['id'] = (int) $row['id'];
        if (isset($row['min_order'])) $row['min_order'] = (int) $row['min_order'];
        if (isset($row['max_order'])) $row['max_order'] = (int) $row['max_order'];
        if (isset($row['order_complete_percentage'])) $row['order_complete_percentage'] = (int) $row['order_complete_percentage'];
        if (isset($row['discount']))  $row['discount']  = (int) $row['discount'];

        $responseBody = [
            'success' => true,
            'message' => 'Loyalty entry updated successfully.',
            'data'    => $row,
        ];

        return new \WP_REST_Response($responseBody, 200);
    }

    private static function map_payload(\WP_REST_Request $request): array
    {
        $min_order = $request->get_param('min_order');
        $max_order = $request->get_param('max_order');
        $order_complete_percentage = $request->get_param('order_complete_percentage');
        $discount  = $request->get_param('discount');
        $status    = $request->get_param('status');

        $payload = [];

        if ($min_order !== null) {
            $payload['min_order'] = absint($min_order);
        }
        if ($max_order !== null) {
            $payload['max_order'] = absint($max_order);
        }
        if ($order_complete_percentage !== null) {
            $payload['order_complete_percentage'] = absint($order_complete_percentage);
        }
        if ($discount !== null) {
            $payload['discount'] = absint($discount);
        }

        if ($status !== null) {
            $payload['status'] = sanitize_text_field($status);
        }

        $payload['updated_at'] = current_time('mysql');

        return $payload;
    }
}
