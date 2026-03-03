<?php
if (!defined('ABSPATH')) exit;

class OrderShowHandle
{
    public static function run(\WP_REST_Request $request): \WP_REST_Response
    {
        global $wpdb;

        $request_data = OrderShowParseRequest::run($request);
        $row = OrderShowFindRow::run($wpdb, $request_data['uniq_id']);

        if (!$row) {
            return OrderShowBuildNotFoundResponse::run();
        }

        $order_data = OrderShowMapRowToOrder::run($row);

        return OrderShowBuildSuccessResponse::run($order_data);
    }
}
