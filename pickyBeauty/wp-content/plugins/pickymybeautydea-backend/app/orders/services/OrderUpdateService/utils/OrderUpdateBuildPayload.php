<?php
if (!defined('ABSPATH')) exit;

class OrderUpdateBuildPayload
{
    public static function run(\WP_REST_Request $request): array
    {
        $data = [];
        $types = [];
        $map = [
            'vendor_id' => '%d',
            'x' => '%f',
            'y' => '%f',
            'price' => '%f',
            'shipping_id' => '%d',
            'budget' => '%f',
            'order_title' => '%s',
            'sharing_status' => '%s',
            'payment_status' => '%s',
        ];

        foreach ($map as $key => $type) {
            if (null !== $request->get_param($key)) {
                $value = $request->get_param($key);
                if ($type === '%s') {
                    $value = sanitize_text_field($value);
                }
                $data[$key] = $value;
                $types[] = $type;
            }
        }

        return [
            'data' => $data,
            'types' => $types,
        ];
    }
}
