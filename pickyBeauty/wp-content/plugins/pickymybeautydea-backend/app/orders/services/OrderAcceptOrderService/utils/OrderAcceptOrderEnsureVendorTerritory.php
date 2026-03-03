<?php
if (!defined('ABSPATH')) exit;

class OrderAcceptOrderEnsureVendorTerritory
{
    public static function run(array $territory)
    {
        if (!OrderVendorTerritoryService::hasZipcode($territory) && !OrderVendorTerritoryService::hasCircle($territory)) {
            return new \WP_REST_Response([
                'status' => false,
                'message' => 'No zipcode or delivery radius found in your profile. Please contact admin.',
            ], 400);
        }

        return null;
    }
}
