<?php
if (!defined('ABSPATH')) exit;

class OrderAcceptOrderEvaluateTerritory
{
    public static function run(array $territory, array $order): array
    {
        return OrderVendorTerritoryService::evaluateOrderMatch(
            $territory,
            $order['zip_code'] ?? null,
            $order['x'] ?? null,
            $order['y'] ?? null
        );
    }
}
