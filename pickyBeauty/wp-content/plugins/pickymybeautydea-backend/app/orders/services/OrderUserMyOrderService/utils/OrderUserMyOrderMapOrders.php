<?php
if (!defined('ABSPATH')) exit;

class OrderUserMyOrderMapOrders
{
    public static function run(array $rows): array
    {
        $orders = [];

        foreach ($rows as $row) {
            $orders[] = [
                'order_unique_id' => $row['order_unique_id'] ?? null,
                'id' => $row['id'] ?? null,
                'sharing_status' => $row['sharing_status'] ?? null,
                'price' => $row['price'] ?? null,
                'budget' => $row['budget'] ?? null,
                'category_id' => $row['category_id'] ?? null,
                'category_name' => $row['category_name'] ?? null,
                'order_details' => $row['order_details'] ?? null,
                'zip_code' => $row['shipping_zip'] ?? null,
                'email' => $row['shipping_email'] ?? null,
                'phone' => $row['shipping_phone'] ?? null,
                'name' => $row['shipping_name'] ?? null,
                'city' => $row['shipping_city'] ?? null,
                'country' => $row['shipping_country'] ?? null,
                'district' => $row['shipping_district'] ?? null,
                'created_at' => $row['created_at'] ?? null,
                'updated_at' => $row['updated_at'] ?? null,
            ];
        }

        return $orders;
    }
}
