<?php
if (!defined('ABSPATH')) exit;

class OrderVendorMyOrderFetchRows
{
    public static function run(
        $wpdb,
        array $territory,
        string $where_sql,
        array $where_params,
        int $per_page,
        int $offset
    ): array {
        $distance_select = order_vendor_my_order_build_distance_select($territory);
        $query_sql = "
            SELECT
                o.*,
                {$distance_select['sql']},
                s.zip_code   AS shipping_zip,
                s.email      AS shipping_email,
                s.phone      AS shipping_phone,
                s.name       AS shipping_name,
                s.city       AS shipping_city,
                s.country    AS shipping_country,
                s.district   AS shipping_district,
                c.title      AS category_name
            FROM {$wpdb->prefix}kib_orders AS o
            LEFT JOIN {$wpdb->prefix}kib_shipping_addresses AS s ON s.id = o.shipping_id
            LEFT JOIN {$wpdb->prefix}kib_categories AS c ON o.category_id = c.id
            {$where_sql}
            ORDER BY o.id DESC
            LIMIT %d OFFSET %d
        ";

        $query_params = array_merge(
            $distance_select['params'],
            $where_params,
            [$per_page, $offset]
        );

        $rows = $wpdb->get_results($wpdb->prepare($query_sql, $query_params), ARRAY_A);

        return is_array($rows) ? $rows : [];
    }
}
