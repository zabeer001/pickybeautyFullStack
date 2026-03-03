<?php
if (!defined('ABSPATH')) exit;

class OrderAcceptOrderParseRequest
{
    public static function run(\WP_REST_Request $request): array
    {
        $order_unique_id = sanitize_text_field($request['order_unique_id']);
        $status = sanitize_text_field($request['sharing_status']);

        return [
            'order_unique_id' => $order_unique_id,
            'status' => $status,
            'normalized_status' => strtolower($status),
        ];
    }
}
