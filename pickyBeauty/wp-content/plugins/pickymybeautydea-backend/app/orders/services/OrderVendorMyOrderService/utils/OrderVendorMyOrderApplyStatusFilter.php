<?php
if (!defined('ABSPATH')) exit;

function order_vendor_my_order_apply_status_filter(string $status_param, string $where_sql, array $where_params, int $user_id): array
{
    if ($status_param === 'unaccepted') {
        $where_sql .= " AND (o.sharing_status IS NULL OR LOWER(o.sharing_status) != 'accepted')";
    } elseif ($status_param === 'accepted') {
        $where_sql .= " AND LOWER(o.sharing_status) = 'accepted' AND o.vendor_id = %d";
        $where_params[] = $user_id;
    }

    return [
        'where_sql' => $where_sql,
        'where_params' => $where_params,
    ];
}
