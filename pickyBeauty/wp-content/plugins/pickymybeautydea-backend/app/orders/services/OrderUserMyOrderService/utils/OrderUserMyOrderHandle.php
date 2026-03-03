<?php
if (!defined('ABSPATH')) exit;

class OrderUserMyOrderHandle
{
    public static function run(\WP_REST_Request $request): \WP_REST_Response
    {
        global $wpdb;

        $current_user = OrderUserMyOrderEnsureUser::run();
        if ($current_user instanceof \WP_REST_Response) {
            return $current_user;
        }

        $user_email = OrderUserMyOrderResolveUserEmail::run($current_user);
        if ($user_email === '') {
            return OrderUserMyOrderBuildNoEmailResponse::run();
        }

        $pagination = OrderUserMyOrderParsePagination::run($request);
        $total_orders = OrderUserMyOrderCount::run($wpdb, $user_email);
        $rows = OrderUserMyOrderFetchRows::run(
            $wpdb,
            $user_email,
            $pagination['per_page'],
            $pagination['offset']
        );
        $orders = OrderUserMyOrderMapOrders::run($rows);

        return OrderUserMyOrderBuildSuccessResponse::run(
            $pagination['page'],
            $pagination['per_page'],
            $total_orders,
            $orders
        );
    }
}
