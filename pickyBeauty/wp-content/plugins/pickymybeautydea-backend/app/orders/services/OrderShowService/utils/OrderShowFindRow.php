<?php
if (!defined('ABSPATH')) exit;

class OrderShowFindRow
{
    public static function run($wpdb, string $uniq_id): ?array
    {
        $orders = $wpdb->prefix . 'kib_orders';
        $shipping = $wpdb->prefix . 'kib_shipping_addresses';
        $categories = $wpdb->prefix . 'kib_categories';
        $users = $wpdb->prefix . 'users';
        $usermeta = $wpdb->prefix . 'usermeta';

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

        return is_array($row) ? $row : null;
    }
}
