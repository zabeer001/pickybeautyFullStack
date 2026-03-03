<?php
if (!defined('ABSPATH')) exit;

class OrderCreateParseRequest
{
    public static function run(\WP_REST_Request $request): array
    {
        return [
            'user_id' => get_current_user_id(),
            'vendor_id' => ($request->get_param('vendor_id') !== null) ? (int) $request->get_param('vendor_id') : null,
            'price' => (float) $request->get_param('budget'),
            'budget' => ($request->get_param('budget') !== null) ? (float) $request->get_param('budget') : null,
            'order_title' => sanitize_text_field($request->get_param('order_title')),
            'order_details' => sanitize_text_field($request->get_param('order_details')),
            'payment_method' => sanitize_text_field($request->get_param('payment_method')),
            'sharing_status' => sanitize_text_field($request->get_param('sharing_status') ?: 'not accepted'),
            'category_id' => sanitize_text_field($request->get_param('category_id')),
            'shipping_id' => ($request->get_param('shipping_id') !== null) ? (int) $request->get_param('shipping_id') : null,
            'raw_shipping' => $request->get_param('shipping'),
        ];
    }
}
