<?php
if (!defined('ABSPATH')) exit;

function order_vendor_my_order_parse_request(\WP_REST_Request $request): array
{
    $status_param = sanitize_text_field($request->get_param('status'));
    $page = max(1, (int) ($request->get_param('page') ?? 1));
    $per_page = min(max(1, (int) ($request->get_param('per_page') ?? 10)), 100);

    return [
        'status_param' => $status_param === '' ? 'unaccepted' : $status_param,
        'page' => $page,
        'per_page' => $per_page,
        'offset' => ($page - 1) * $per_page,
    ];
}
