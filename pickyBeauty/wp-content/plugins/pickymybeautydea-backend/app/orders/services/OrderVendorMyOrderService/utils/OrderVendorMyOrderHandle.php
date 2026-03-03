<?php
if (!defined('ABSPATH')) exit;

class OrderVendorMyOrderHandle
{
    public static function run(\WP_REST_Request $request): \WP_REST_Response
    {
        global $wpdb;

        $current_user = OrderVendorMyOrderEnsureUser::run();
        if ($current_user instanceof \WP_REST_Response) {
            return $current_user;
        }

        $context = OrderVendorMyOrderBuildContext::run($request, (int) $current_user->ID);
        if ($context instanceof \WP_REST_Response) {
            return $context;
        }

        $total_orders = OrderVendorMyOrderCount::run(
            $wpdb,
            $context['status_filter']['where_sql'],
            $context['status_filter']['where_params']
        );
        $rows = OrderVendorMyOrderFetchRows::run(
            $wpdb,
            $context['territory'],
            $context['status_filter']['where_sql'],
            $context['status_filter']['where_params'],
            $context['request_config']['per_page'],
            $context['request_config']['offset']
        );
        $orders_data = order_vendor_my_order_map_orders(
            $rows,
            $context['request_config']['status_param']
        );

        return order_vendor_my_order_build_response(
            $context['territory'],
            $context['request_config']['status_param'],
            $context['request_config']['page'],
            $context['request_config']['per_page'],
            $total_orders,
            $orders_data
        );
    }
}
