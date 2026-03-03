<?php
if (!defined('ABSPATH')) exit;

class OrderCreateBuildOrderData
{
    public static function run(array $request_data, ?int $customer_id, ?int $shipping_id, ?float $x_value, ?float $y_value, string $order_unique_id): array
    {
        return [
            'user_id' => $request_data['user_id'] ?: null,
            'vendor_id' => $request_data['vendor_id'] ?: null,
            'customer_id' => $customer_id ?: null,
            'price' => $request_data['price'],
            'shipping_id' => $shipping_id ?: null,
            'budget' => $request_data['budget'],
            'order_title' => $request_data['order_title'] ?: null,
            'order_details' => $request_data['order_details'] ?: null,
            'payment_method' => $request_data['payment_method'] ?: null,
            'order_unique_id' => $order_unique_id,
            'sharing_status' => $request_data['sharing_status'],
            'category_id' => (int) $request_data['category_id'],
            'x' => $x_value,
            'y' => $y_value,
            'created_at' => current_time('mysql'),
            'updated_at' => current_time('mysql'),
        ];
    }
}
