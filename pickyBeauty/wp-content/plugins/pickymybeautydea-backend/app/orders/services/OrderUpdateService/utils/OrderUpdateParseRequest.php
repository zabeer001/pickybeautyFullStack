<?php
if (!defined('ABSPATH')) exit;

class OrderUpdateParseRequest
{
    public static function run(\WP_REST_Request $request): array
    {
        return [
            'id' => (int) $request['id'],
            'shipping' => $request->get_param('shipping'),
        ];
    }
}
