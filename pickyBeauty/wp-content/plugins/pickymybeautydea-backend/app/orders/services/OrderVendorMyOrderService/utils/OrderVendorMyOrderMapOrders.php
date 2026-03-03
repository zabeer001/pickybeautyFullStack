<?php
if (!defined('ABSPATH')) exit;

function order_vendor_my_order_map_orders(array $orders, string $status_param): array
{
    $orders_data = [];

    foreach ($orders as $row) {
        $distance_km = isset($row['distance_km']) && is_numeric($row['distance_km'])
            ? round((float) $row['distance_km'], 2)
            : null;

        $base_payload = [
            'id' => $row['id'] ?? null,
            'order_unique_id' => $row['order_unique_id'] ?? null,
            'sharing_status' => $row['sharing_status'] ?? null,
            'category_id' => $row['category_id'] ?? null,
            'category_name' => $row['category_name'] ?? null,
            'order_details' => $row['order_details'] ?? null,
            'zip_code' => $row['shipping_zip'] ?? null,
            'budget' => $row['budget'] ?? null,
            'x' => isset($row['x']) ? (float) $row['x'] : null,
            'y' => isset($row['y']) ? (float) $row['y'] : null,
            'distance_km' => $distance_km,
            'created_at' => $row['created_at'] ?? null,
            'updated_at' => $row['updated_at'] ?? null,
        ];

        if ($status_param === 'unaccepted') {
            $orders_data[] = $base_payload;
            continue;
        }

        $orders_data[] = $base_payload + [
            'total_amount' => $row['total_amount'] ?? null,
            'email' => $row['shipping_email'] ?? null,
            'phone' => $row['shipping_phone'] ?? null,
            'name' => $row['shipping_name'] ?? null,
            'city' => $row['shipping_city'] ?? null,
            'country' => $row['shipping_country'] ?? null,
            'district' => $row['shipping_district'] ?? null,
        ];
    }

    return $orders_data;
}
