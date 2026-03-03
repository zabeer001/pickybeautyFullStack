<?php
if (!defined('ABSPATH')) exit;

function order_vendor_my_order_build_distance_select(array $territory): array
{
    if (!OrderVendorTerritoryService::hasCircle($territory)) {
        return [
            'sql' => 'NULL AS distance_km',
            'params' => [],
        ];
    }

    $distance_expression = OrderVendorTerritoryService::buildDistanceExpression(
        (float) $territory['latitude'],
        (float) $territory['longitude'],
        'o.x',
        'o.y'
    );

    return [
        'sql' => $distance_expression['sql'] . ' AS distance_km',
        'params' => $distance_expression['params'],
    ];
}
