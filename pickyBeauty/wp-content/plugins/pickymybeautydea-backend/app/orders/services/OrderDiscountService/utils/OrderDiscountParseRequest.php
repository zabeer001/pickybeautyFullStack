<?php
if (!defined('ABSPATH')) exit;

class OrderDiscountParseRequest
{
    public static function run(\WP_REST_Request $request): array
    {
        return [
            'email' => sanitize_email($request->get_param('email')),
            'budget' => (float) $request->get_param('budget'),
        ];
    }
}
