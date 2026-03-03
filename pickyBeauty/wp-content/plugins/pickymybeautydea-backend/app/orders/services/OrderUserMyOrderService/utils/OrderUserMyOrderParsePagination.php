<?php
if (!defined('ABSPATH')) exit;

class OrderUserMyOrderParsePagination
{
    public static function run(\WP_REST_Request $request): array
    {
        $page = max(1, (int) ($request->get_param('page') ?? 1));
        $per_page = min(max(1, (int) ($request->get_param('per_page') ?? 10)), 100);

        return [
            'page' => $page,
            'per_page' => $per_page,
            'offset' => ($page - 1) * $per_page,
        ];
    }
}
