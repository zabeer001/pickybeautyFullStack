<?php
if (!defined('ABSPATH')) exit;

class OrderIndexService
{
    public static function index(\WP_REST_Request $request)
    {
        global $wpdb;

        $orders_table   = $wpdb->prefix . 'kib_orders';
        $shipping_table = $wpdb->prefix . 'kib_shipping_addresses';
        $category_table = $wpdb->prefix . 'kib_categories';

        $page      = max(1, intval($request->get_param('page') ?? 1));
        $per_page  = min(max(1, intval($request->get_param('per_page') ?? 10)), 100);
        $offset    = ($page - 1) * $per_page;

        $search          = sanitize_text_field($request->get_param('search'));
        $vendor_id       = intval($request->get_param('vendor_id'));
        $category_id     = intval($request->get_param('category_id'));
        $sharing_status  = sanitize_text_field($request->get_param('sharing_status'));
        $payment_status  = sanitize_text_field($request->get_param('payment_status'));

        if (!empty($sharing_status) && strtolower($sharing_status) === 'all') {
            $sharing_status = '';
        }
        if (!empty($payment_status) && strtolower($payment_status) === 'all') {
            $payment_status = '';
        }

        $where_clauses = [];
        $params        = [];

        if (!empty($search)) {
            $like = '%' . $wpdb->esc_like($search) . '%';
            $where_clauses[] = "(
            o.order_unique_id LIKE %s
            OR s.email LIKE %s
            OR s.phone LIKE %s
            OR s.zip_code LIKE %s
        )";
            $params = array_merge($params, [$like, $like, $like, $like]);
        }

        if (!empty($vendor_id)) {
            $where_clauses[] = "o.vendor_id = %d";
            $params[] = $vendor_id;
        }

        if (!empty($category_id)) {
            $where_clauses[] = "o.category_id = %d";
            $params[] = $category_id;
        }

        if (!empty($sharing_status)) {
            $normalized_status = strtolower($sharing_status);
            if ($normalized_status === 'not accetped') {
                $sharing_status = 'not accepted';
                $normalized_status = 'not accepted';
            }

            if ($normalized_status === 'not accepted') {
                $where_clauses[] = "(o.sharing_status IS NULL OR o.sharing_status = '' OR o.sharing_status = '0' OR o.sharing_status = %s)";
                $params[] = $sharing_status;
            } else {
                $where_clauses[] = "o.sharing_status = %s";
                $params[] = $sharing_status;
            }
        }

        if (!empty($payment_status)) {
            $where_clauses[] = "o.payment_status = %s";
            $params[] = $payment_status;
        }

        $where_sql = '';
        if (!empty($where_clauses)) {
            $where_sql = 'WHERE ' . implode(' AND ', $where_clauses);
        }

        $count_sql = "
        SELECT COUNT(*)
        FROM {$orders_table} AS o
        LEFT JOIN {$shipping_table} AS s ON o.shipping_id = s.id
        {$where_sql}
    ";

        $total_orders = !empty($params)
            ? (int) $wpdb->get_var($wpdb->prepare($count_sql, $params))
            : (int) $wpdb->get_var("
            SELECT COUNT(*)
            FROM {$orders_table} AS o
            LEFT JOIN {$shipping_table} AS s ON o.shipping_id = s.id
        ");

        $query_sql = "
        SELECT 
            o.*,
            s.email      AS email,
            s.phone      AS phone,
            s.zip_code   AS zip_code,
            s.name       AS shipping_name,
            s.city       AS shipping_city,
            s.country    AS shipping_country,
            s.district   AS shipping_district,
            c.id         AS category_ref_id,
            c.title      AS category_name
        FROM {$orders_table} AS o
        LEFT JOIN {$shipping_table} AS s ON o.shipping_id = s.id
        LEFT JOIN {$category_table} AS c ON o.category_id = c.id
        {$where_sql}
        ORDER BY o.id DESC
        LIMIT %d OFFSET %d
    ";

        $query_params = array_merge($params, [$per_page, $offset]);
        $orders_raw = $wpdb->get_results($wpdb->prepare($query_sql, $query_params), ARRAY_A);
        $orders = array_map(function ($order) {
            $order['category'] = [
                'id'    => isset($order['category_ref_id']) ? (int) $order['category_ref_id'] : null,
                'title' => $order['category_name'] ?? null,
            ];

            unset($order['category_ref_id']);

            return $order;
        }, $orders_raw ?: []);

        $total_pages = (int) ceil($total_orders / $per_page);

        return new \WP_REST_Response([
            'status'         => true,
            'message'        => 'Orders fetched successfully.',
            'pagination'     => [
                'current_page'  => $page,
                'per_page'      => $per_page,
                'total_orders'  => $total_orders,
                'total_pages'   => $total_pages,
            ],
            'orders'         => $orders,
        ], 200);
    }
}
