<?php
if (!defined('ABSPATH')) exit;

class OrderShowParseRequest
{
    public static function run(\WP_REST_Request $request): array
    {
        return [
            'uniq_id' => sanitize_text_field($request['uniq_id']),
        ];
    }
}
