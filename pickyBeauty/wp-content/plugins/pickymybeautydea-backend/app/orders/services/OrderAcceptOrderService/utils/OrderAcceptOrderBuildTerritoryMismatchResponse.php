<?php
if (!defined('ABSPATH')) exit;

class OrderAcceptOrderBuildTerritoryMismatchResponse
{
    public static function run(array $territory, array $order, array $territory_match): \WP_REST_Response
    {
        return new \WP_REST_Response([
            'status' => false,
            'message' => 'You can only accept orders inside your zipcode or delivery radius.',
            'user_zipcode' => $territory['zipcode'],
            'order_zipcode' => $order['zip_code'] ?? null,
            'vendor_radius_km' => $territory['radius_km'],
            'distance_km' => $territory_match['distance_km'] !== null
                ? round((float) $territory_match['distance_km'], 2)
                : null,
        ], 403);
    }
}
