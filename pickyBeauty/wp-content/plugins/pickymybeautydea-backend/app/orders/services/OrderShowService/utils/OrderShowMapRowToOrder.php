<?php
if (!defined('ABSPATH')) exit;

class OrderShowMapRowToOrder
{
    public static function run(array $row): array
    {
        return [
            'id' => (int) $row['id'],
            'order_unique_id' => $row['order_unique_id'],
            'user_id' => $row['user_id'] ? (int) $row['user_id'] : null,
            'vendor_id' => $row['vendor_id'] ? (int) $row['vendor_id'] : null,
            'x' => isset($row['x']) ? (float) $row['x'] : null,
            'y' => isset($row['y']) ? (float) $row['y'] : null,
            'price' => (float) $row['price'],
            'shipping_id' => $row['shipping_id'] ? (int) $row['shipping_id'] : null,
            'budget' => (float) $row['budget'],
            'order_title' => $row['order_title'],
            'order_details' => $row['order_details'],
            'sharing_status' => $row['sharing_status'],
            'payment_status' => $row['payment_status'],
            'created_at' => $row['created_at'],
            'updated_at' => $row['updated_at'],
            'shipping' => [
                'id' => $row['s_id'] ?? null,
                'name' => $row['s_name'] ?? null,
                'email' => $row['s_email'] ?? null,
                'phone' => $row['s_phone'] ?? null,
                'country' => $row['s_country'] ?? null,
                'city' => $row['s_city'] ?? null,
                'district' => $row['s_district'] ?? null,
                'zip_code' => $row['s_zip_code'] ?? null,
                'created_at' => $row['s_created_at'] ?? null,
                'updated_at' => $row['s_updated_at'] ?? null,
            ],
            'category' => [
                'id' => isset($row['c_id']) ? (int) $row['c_id'] : null,
                'title' => isset($row['c_title']) ? $row['c_title'] : null,
            ],
            'vendor' => [
                'id' => $row['u_id'] ?? null,
                'name' => $row['u_name'] ?? null,
                'email' => $row['u_email'] ?? null,
            ],
        ];
    }
}
