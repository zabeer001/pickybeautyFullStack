<?php
if (!defined('ABSPATH')) exit;

class OrderUserMyOrderFetchRows
{
    public static function run($wpdb, string $user_email, int $per_page, int $offset): array
    {
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
            FROM {$wpdb->prefix}kib_orders AS o
            LEFT JOIN {$wpdb->prefix}kib_shipping_addresses AS s ON s.id = o.shipping_id
            LEFT JOIN {$wpdb->prefix}kib_categories AS c ON o.category_id = c.id
            WHERE s.email = %s
            ORDER BY o.id DESC
            LIMIT %d OFFSET %d
        ";

        $rows = $wpdb->get_results(
            $wpdb->prepare($query_sql, $user_email, $per_page, $offset),
            ARRAY_A
        );

        return is_array($rows) ? $rows : [];
    }
}
