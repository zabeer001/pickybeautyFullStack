<?php
if (!defined('ABSPATH')) exit;

class OrderUserMyOrderService
{
    public static function userMyOrder(\WP_REST_Request $request)
    {
        global $wpdb;

        $orders_table   = $wpdb->prefix . 'kib_orders';
        $shipping_table = $wpdb->prefix . 'kib_shipping_addresses';
        $category_table = $wpdb->prefix . 'kib_categories';

        $current_user = wp_get_current_user();
        if (!$current_user || empty($current_user->ID)) {
            return new \WP_REST_Response([
                'status'  => false,
                'message' => 'Unauthorized. Please log in.',
            ], 401);
        }

        $user_email = isset($current_user->user_email) ? sanitize_email($current_user->user_email) : '';
        if (empty($user_email)) {
            return new \WP_REST_Response([
                'status'  => true,
                'message' => 'No email associated with the current user.',
                'current_page' => 1,
                'per_page'     => 0,
                'total_orders' => 0,
                'total_pages'  => 0,
                'orders'       => [],
            ], 200);
        }

        $page     = max(1, intval($request->get_param('page') ?? 1));
        $per_page = min(max(1, intval($request->get_param('per_page') ?? 10)), 100);
        $offset   = ($page - 1) * $per_page;

        // Count total orders for this user by email (shipping email)
        $count_sql = "
            SELECT COUNT(*)
            FROM {$orders_table} AS o
            LEFT JOIN {$shipping_table} AS s ON s.id = o.shipping_id
            WHERE s.email = %s
        ";

        $total_orders = (int) $wpdb->get_var(
            $wpdb->prepare($count_sql, $user_email)
        );

        // Fetch orders with shipping + category info
        $query_sql = "
            SELECT
                o.*,
                s.zip_code   AS shipping_zip,
                s.email      AS shipping_email,
                s.phone      AS shipping_phone,
                s.name       AS shipping_name,
                s.city       AS shipping_city,
                s.country    AS shipping_country,
                s.district   AS shipping_district,
                c.title      AS category_name
            FROM {$orders_table} AS o
            LEFT JOIN {$shipping_table} AS s ON s.id = o.shipping_id
            LEFT JOIN {$category_table} AS c ON o.category_id = c.id
            WHERE s.email = %s
            ORDER BY o.id DESC
            LIMIT %d OFFSET %d
        ";

        $orders = $wpdb->get_results(
            $wpdb->prepare($query_sql, $user_email, $per_page, $offset),
            ARRAY_A
        );

        $total_pages = (int) ceil($total_orders / $per_page);

        $ordersData = [];
        foreach ($orders as $row) {
            $ordersData[] = [
                'order_unique_id' => $row['order_unique_id'] ?? null,
                'id'              => $row['id'] ?? null,
                'sharing_status'  => $row['sharing_status'] ?? null,
                'price'           => $row['price'] ?? null,
                'budget'          => $row['budget'] ?? null,
                'category_id'     => $row['category_id'] ?? null,
                'category_name'   => $row['category_name'] ?? null,
                'order_details'   => $row['order_details'] ?? null,
                'zip_code'        => $row['shipping_zip'] ?? null,
                'email'           => $row['shipping_email'] ?? null,
                'phone'           => $row['shipping_phone'] ?? null,
                'name'            => $row['shipping_name'] ?? null,
                'city'            => $row['shipping_city'] ?? null,
                'country'         => $row['shipping_country'] ?? null,
                'district'        => $row['shipping_district'] ?? null,
                'created_at'      => $row['created_at'] ?? null,
                'updated_at'      => $row['updated_at'] ?? null,
            ];
        }

        return new \WP_REST_Response([
            'status'        => true,
            'message'       => 'User orders fetched successfully.',
            'current_page'  => $page,
            'per_page'      => $per_page,
            'total_orders'  => $total_orders,
            'total_pages'   => $total_pages,
            'orders'        => $ordersData,
        ], 200);
    }
}
