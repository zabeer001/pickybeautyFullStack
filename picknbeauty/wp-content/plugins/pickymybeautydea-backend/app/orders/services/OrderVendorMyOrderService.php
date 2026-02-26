<?php
if (!defined('ABSPATH')) exit;

class OrderVendorMyOrderService
{
    public static function vendorMyOrder(\WP_REST_Request $request)
    {
        global $wpdb;

        $orders_table   = $wpdb->prefix . 'kib_orders';
        $shipping_table = $wpdb->prefix . 'kib_shipping_addresses';
        $category_table = $wpdb->prefix . 'kib_categories';
        $xy_table       = $wpdb->prefix . 'kib_xy';

        // Require an authenticated vendor.
        $current_user = wp_get_current_user();
        if (!$current_user || empty($current_user->ID)) {
            return new \WP_REST_Response([
                'status'  => false,
                'message' => 'Unauthorized. Please log in.',
            ], 401);
        }

        $user_id = $current_user->ID;

        // Vendor location filters:
        // 1) Zipcode from user meta.
        // 2) Optional circle (x,y,radius) from kib_xy table.
        $user_zipcode = get_user_meta($user_id, 'zipcode', true);
        $xy_row = $wpdb->get_row(
            $wpdb->prepare(
                "SELECT x, y, radius FROM {$xy_table} WHERE user_id = %d ORDER BY id DESC LIMIT 1",
                $user_id
            ),
            ARRAY_A
        );
        $has_circle = is_array($xy_row) && isset($xy_row['radius']) && (float) $xy_row['radius'] > 0;
        $circle_x = $has_circle ? (float) $xy_row['x'] : null;
        $circle_y = $has_circle ? (float) $xy_row['y'] : null;
        $circle_r = $has_circle ? (float) $xy_row['radius'] : null;

        if (empty($user_zipcode) && !$has_circle) {
            return new \WP_REST_Response([
                'status'  => false,
                'message' => 'No zipcode or radius found for this vendor.',
                'orders'  => [],
            ], 200);
        }

        // Pagination + status filter.
        $status_param = sanitize_text_field($request->get_param('status'));
        $page         = max(1, intval($request->get_param('page') ?? 1));
        $per_page     = min(max(1, intval($request->get_param('per_page') ?? 10)), 100);
        $offset       = ($page - 1) * $per_page;

        if (empty($status_param)) {
            $status_param = 'unaccepted';
        }

        // Build location filter: zipcode OR within circle radius (if provided).
        $where_parts = [];
        $params      = [];

        if (!empty($user_zipcode)) {
            $where_parts[] = "s.zip_code = %s";
            $params[] = $user_zipcode;
        }

        if ($has_circle) {
            $where_parts[] = "(
                o.x IS NOT NULL
                AND o.y IS NOT NULL
                AND ((o.x - %f) * (o.x - %f) + (o.y - %f) * (o.y - %f) <= %f)
            )";
            $params[] = $circle_x;
            $params[] = $circle_x;
            $params[] = $circle_y;
            $params[] = $circle_y;
            $params[] = $circle_r * $circle_r;
        }

        $where_sql = "WHERE (" . implode(" OR ", $where_parts) . ")";

        // Status-specific constraints.
        if ($status_param === 'unaccepted') {
            $where_sql .= " AND (o.sharing_status IS NULL OR o.sharing_status != 'accepted')";
        } elseif ($status_param === 'accepted') {
            $where_sql .= " AND o.sharing_status = 'accepted' AND o.vendor_id = %d";
            $params[] = $user_id;
        }

        // Count for pagination.
        $count_sql = "
        SELECT COUNT(*)
        FROM {$orders_table} AS o
        LEFT JOIN {$shipping_table} AS s ON o.shipping_id = s.id
        {$where_sql}
    ";
        $total_orders = (int) $wpdb->get_var($wpdb->prepare($count_sql, $params));

        // Fetch paginated orders + related shipping/category data.
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
            c.title      As category_name

        FROM {$orders_table} AS o
        LEFT JOIN {$shipping_table} AS s ON s.id = o.shipping_id
        LEFT JOIN {$category_table} AS c ON o.category_id = c.id
        {$where_sql}
        ORDER BY o.id DESC
        LIMIT %d OFFSET %d
    ";

        $params[] = $per_page;
        $params[] = $offset;

        $orders = $wpdb->get_results($wpdb->prepare($query_sql, $params), ARRAY_A);

        $total_pages = (int) ceil($total_orders / $per_page);

        // Shape response payload by status.
        $ordersData = [];
        foreach ($orders as $row) {
            if ($status_param === 'unaccepted') {
                $ordersData[] = [
                    'id' => $row['id'] ?? null,
                    'order_unique_id' => $row['order_unique_id'] ?? null,
                    'sharing_status'  => $row['sharing_status'] ?? null,
                    'zip_code'        => $row['shipping_zip'] ?? null,
                    'category_id'     => $row['category_id'] ?? null,
                    'category_name'   => $row['category_name'] ?? null,
                    'order_details'   => $row['order_details'] ?? null,
                    'budget'          => $row['budget'] ?? null,
                    'created_at'      => $row['created_at'] ?? null,
                    'updated_at'      => $row['updated_at'] ?? null,
                ];
            } else {
                $ordersData[] = [
                    'order_unique_id' => $row['order_unique_id'] ?? null,
                    'sharing_status'  => $row['sharing_status'] ?? null,
                    'total_amount'    => $row['total_amount'] ?? null,
                    'category_id'     => $row['category_id'] ?? null,
                    'category_name'   => $row['category_name'] ?? null,
                    'order_details'   => $row['order_details'] ?? null,
                    'zip_code'        => $row['shipping_zip'] ?? null,
                    'budget'          => $row['budget'] ?? null,
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
        }

        return new \WP_REST_Response([
            'status'        => true,
            'message'       => 'Orders fetched successfully.',
            'filter'        => $status_param,
            'user_zipcode'  => $user_zipcode,
            'current_page'  => $page,
            'per_page'      => $per_page,
            'total_orders'  => $total_orders,
            'total_pages'   => $total_pages,
            'orders'        => $ordersData,
        ], 200);
    }
}
