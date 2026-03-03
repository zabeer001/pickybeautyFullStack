<?php
if (!defined('ABSPATH')) exit;

function order_vendor_my_order_build_response(
    array $territory,
    string $status_param,
    int $page,
    int $per_page,
    int $total_orders,
    array $orders_data
): \WP_REST_Response
{
    $total_pages = (int) ceil($total_orders / $per_page);

    return new \WP_REST_Response([
        'status' => true,
        'message' => 'Orders fetched successfully.',
        'filter' => $status_param,
        'user_zipcode' => $territory['zipcode'],
        'vendor_latitude' => $territory['latitude'],
        'vendor_longitude' => $territory['longitude'],
        'vendor_radius_km' => $territory['radius_km'],
        'current_page' => $page,
        'per_page' => $per_page,
        'total_orders' => $total_orders,
        'total_pages' => $total_pages,
        'orders' => $orders_data,
    ], 200);
}
