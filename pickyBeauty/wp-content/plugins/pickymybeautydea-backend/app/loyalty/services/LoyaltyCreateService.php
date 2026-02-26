<?php
if (!defined('ABSPATH')) {
    exit;
}

class LoyaltyCreateService
{
    public static function create(\WP_REST_Request $request)
    {
        global $wpdb;
        $table = $wpdb->prefix . 'kib_loyalty';

        $payload = self::map_payload($request);

        if (empty($payload)) {
            return new \WP_Error('kib_loyalty_create_empty', 'No fields provided.', ['status' => 400]);
        }

        $payload['created_at'] = current_time('mysql');
        $payload['updated_at'] = current_time('mysql');
        if (empty($payload['status'])) {
            $payload['status'] = 'active';
        }

        $formats = ['%d', '%d', '%d', '%d', '%s', '%s'];
        $ok = $wpdb->insert($table, $payload, $formats);
        if (!$ok) {
            return new \WP_Error('kib_loyalty_create_failed', 'Failed to create loyalty entry.', ['status' => 500]);
        }

        $id = (int) $wpdb->insert_id;

        $row = $wpdb->get_row($wpdb->prepare("SELECT * FROM {$table} WHERE id = %d", $id), ARRAY_A);

        if (null === $row) {
            return new \WP_Error('kib_loyalty_fetch_failed', 'Created entry not found.', ['status' => 500]);
        }

        $row['id'] = (int) $row['id'];
        if (isset($row['min_order'])) $row['min_order'] = (int) $row['min_order'];
        if (isset($row['max_order'])) $row['max_order'] = (int) $row['max_order'];
        if (isset($row['order_complete_percentage'])) $row['order_complete_percentage'] = (int) $row['order_complete_percentage'];
        if (isset($row['discount']))  $row['discount']  = (int) $row['discount'];

        $responseBody = [
            'success' => true,
            'message' => 'Loyalty entry created successfully.',
            'data'    => $row,
        ];

        $response = new \WP_REST_Response($responseBody, 201);
        $response->header('Location', rest_url("kibsterlp-admin/v1/loyalty/{$id}"));

        return $response;
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
