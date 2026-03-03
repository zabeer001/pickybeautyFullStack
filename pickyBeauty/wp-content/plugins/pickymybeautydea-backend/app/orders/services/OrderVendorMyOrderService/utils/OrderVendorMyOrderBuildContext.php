<?php
if (!defined('ABSPATH')) exit;

class OrderVendorMyOrderBuildContext
{
    public static function run(\WP_REST_Request $request, int $user_id)
    {
        $territory = OrderVendorTerritoryService::getVendorTerritory($user_id);

        if (!OrderVendorTerritoryService::hasZipcode($territory) && !OrderVendorTerritoryService::hasCircle($territory)) {
            return new \WP_REST_Response([
                'status' => false,
                'message' => 'No zipcode or radius found for this vendor.',
                'orders' => [],
            ], 200);
        }

        $request_config = order_vendor_my_order_parse_request($request);
        $territory_clause = OrderVendorTerritoryService::buildOrderTerritoryWhereClause(
            $territory,
            's.zip_code',
            'o.x',
            'o.y'
        );

        return [
            'territory' => $territory,
            'request_config' => $request_config,
            'status_filter' => order_vendor_my_order_apply_status_filter(
                $request_config['status_param'],
                $territory_clause['sql'],
                $territory_clause['params'],
                $user_id
            ),
        ];
    }
}
