<?php
if (!defined('ABSPATH')) exit;

class OrderCreateResolveCoordinateValue
{
    public static function run(\WP_REST_Request $request, ?array $shipping, string $axis): ?float
    {
        $request_value = OrderCreateSanitizeCoordinateValue::run($request->get_param($axis));
        if ($request_value !== null) {
            return $request_value;
        }

        if (is_array($shipping) && array_key_exists($axis, $shipping)) {
            return OrderCreateSanitizeCoordinateValue::run($shipping[$axis]);
        }

        return null;
    }
}
