<?php
if (!defined('ABSPATH')) exit;

class OrderShowService
{
    public static function show(\WP_REST_Request $request)
    {
        global $wpdb;

        $orders     = $wpdb->prefix . 'kib_orders';
        $shipping   = $wpdb->prefix . 'kib_shipping_addresses';
        $categories = $wpdb->prefix . 'kib_categories';
        $users      = $wpdb->prefix . 'users';
        $usermeta   = $wpdb->prefix . 'usermeta';

        $uniq_id = sanitize_text_field($request['uniq_id']);

        $sql = "
        SELECT
            o.*,
            s.id         AS s_id,
            s.email      AS s_email,
            s.phone      AS s_phone,
            s.name       AS s_name,
            s.country    AS s_country,
            s.city       AS s_city,
            s.district   AS s_district,
            s.zip_code   AS s_zip_code,
            s.created_at AS s_created_at,
            s.updated_at AS s_updated_at,
            c.id         AS c_id,
            c.title      AS c_title,
            u.ID         AS u_id,
            u.user_email AS u_email,
            u.display_name AS u_name
        FROM {$orders} o
        LEFT JOIN {$shipping} s ON s.id = o.shipping_id
        LEFT JOIN {$categories} c ON c.id = o.category_id
        LEFT JOIN {$users} u ON u.ID = o.vendor_id
        LEFT JOIN {$usermeta} um ON um.user_id = u.ID AND um.meta_key = '{$wpdb->prefix}capabilities'
        WHERE o.order_unique_id = %s
          AND (um.meta_value LIKE '%vendor%' OR u.ID IS NULL)
        LIMIT 1
    ";

        $row = $wpdb->get_row($wpdb->prepare($sql, $uniq_id), ARRAY_A);

        if (!$row) {
            return new \WP_REST_Response([
                'status'  => false,
                'message' => 'Order not found',
                'data'    => null,
            ], 404);
        }

        $orderData = self::mapRowToOrder($row);

        return new \WP_REST_Response([
            'status'  => true,
            'message' => 'Order retrieved successfully',
            'data'    => $orderData,
        ], 200);
    }

    private static function mapRowToOrder($row)
    {
        return [
            'id'              => (int) $row['id'],
            'order_unique_id' => $row['order_unique_id'],
            'user_id'         => $row['user_id'] ? (int) $row['user_id'] : null,
            'vendor_id'       => $row['vendor_id'] ? (int) $row['vendor_id'] : null,
            'x'               => isset($row['x']) ? (float) $row['x'] : null,
            'y'               => isset($row['y']) ? (float) $row['y'] : null,
            'price'           => (float) $row['price'],
            'shipping_id'     => $row['shipping_id'] ? (int) $row['shipping_id'] : null,
            'budget'          => (float) $row['budget'],
            'order_title'     => $row['order_title'],
            'order_details'   => $row['order_details'],
            'sharing_status'  => $row['sharing_status'],
            'payment_status'  => $row['payment_status'],
            'created_at'      => $row['created_at'],
            'updated_at'      => $row['updated_at'],
            'shipping' => [
                'id'         => $row['s_id'] ?? null,
                'name'       => $row['s_name'] ?? null,
                'email'      => $row['s_email'] ?? null,
                'phone'      => $row['s_phone'] ?? null,
                'country'    => $row['s_country'] ?? null,
                'city'       => $row['s_city'] ?? null,
                'district'   => $row['s_district'] ?? null,
                'zip_code'   => $row['s_zip_code'] ?? null,
                'created_at' => $row['s_created_at'] ?? null,
                'updated_at' => $row['s_updated_at'] ?? null,
            ],
            'category' => [
                'id'    => isset($row['c_id']) ? (int) $row['c_id'] : null,
                'title' => isset($row['c_title']) ? $row['c_title'] : null,
            ],
            'vendor' => [
                'id'    => $row['u_id'] ?? null,
                'name'  => $row['u_name'] ?? null,
                'email' => $row['u_email'] ?? null,
            ],
        ];
    }
}
