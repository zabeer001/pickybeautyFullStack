<?php
if (!defined('ABSPATH')) exit;

class OrderUpdateBuildSuccessResponse
{
    public static function run(int $updated, int $id, ?string $payment_status_compare, ?string $sharing_status_compare, ?string $email, $customer_updated, $customer_cancel_updated): \WP_REST_Response
    {
        return new \WP_REST_Response([
            'status' => true,
            'message' => 'Order updated successfully',
            'updated' => $updated,
            'order_id' => $id,
            'payment_status_compare' => $payment_status_compare,
            'sharing_status_compare' => $sharing_status_compare,
            'customer_email_used' => $email,
            'customer_updated' => $customer_updated,
            'customer_cancel_updated' => $customer_cancel_updated,
        ], 200);
    }
}
