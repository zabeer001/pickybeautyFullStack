<?php
if (!defined('ABSPATH')) exit;

class OrderUserMyOrderBuildSuccessResponse
{
    public static function run(int $page, int $per_page, int $total_orders, array $orders): \WP_REST_Response
    {
        $total_pages = (int) ceil($total_orders / $per_page);

        return new \WP_REST_Response([
            'status' => true,
            'message' => 'User orders fetched successfully.',
            'current_page' => $page,
            'per_page' => $per_page,
            'total_orders' => $total_orders,
            'total_pages' => $total_pages,
            'orders' => $orders,
        ], 200);
    }
}
