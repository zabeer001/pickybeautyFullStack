<?php
if (!defined('ABSPATH')) exit;

class OrderVendorTerritoryService
{
    public static function getVendorTerritory(int $user_id): array
    {
        global $wpdb;

        $xy_table = $wpdb->prefix . 'kib_xy';
        $user_zipcode = self::normalizeZipcode(get_user_meta($user_id, 'zipcode', true));

        $xy_row = $wpdb->get_row(
            $wpdb->prepare(
                "SELECT x, y, radius, status FROM {$xy_table} WHERE user_id = %d ORDER BY id DESC LIMIT 1",
                $user_id
            ),
            ARRAY_A
        );

        $latitude = self::normalizeCoordinate($xy_row['x'] ?? null);
        $longitude = self::normalizeCoordinate($xy_row['y'] ?? null);
        $radius_km = isset($xy_row['radius']) ? max(0, (float) $xy_row['radius']) : 0.0;
        $status = strtolower(trim((string) ($xy_row['status'] ?? 'active')));
        $location_enabled = $status !== 'inactive';
        $has_circle = $location_enabled && $latitude !== null && $longitude !== null && $radius_km > 0;

        return [
            'zipcode' => $user_zipcode,
            'latitude' => $latitude,
            'longitude' => $longitude,
            'radius_km' => $radius_km,
            'location_enabled' => $location_enabled,
            'has_circle' => $has_circle,
        ];
    }

    public static function hasZipcode(array $territory): bool
    {
        return !empty($territory['zipcode']);
    }

    public static function hasCircle(array $territory): bool
    {
        return !empty($territory['has_circle']);
    }

    public static function buildOrderTerritoryWhereClause(
        array $territory,
        string $shippingZipColumn = 's.zip_code',
        string $orderLatitudeColumn = 'o.x',
        string $orderLongitudeColumn = 'o.y'
    ): array {
        $where_parts = [];
        $params = [];

        if (self::hasZipcode($territory)) {
            $where_parts[] = "{$shippingZipColumn} = %s";
            $params[] = $territory['zipcode'];
        }

        if (self::hasCircle($territory)) {
            $distance = self::buildDistanceExpression(
                (float) $territory['latitude'],
                (float) $territory['longitude'],
                $orderLatitudeColumn,
                $orderLongitudeColumn
            );

            $where_parts[] = "(
                {$orderLatitudeColumn} IS NOT NULL
                AND {$orderLongitudeColumn} IS NOT NULL
                AND {$distance['sql']} <= %f
            )";
            $params = array_merge($params, $distance['params'], [(float) $territory['radius_km']]);
        }

        if (empty($where_parts)) {
            return [
                'sql' => '',
                'params' => [],
            ];
        }

        return [
            'sql' => 'WHERE (' . implode(' OR ', $where_parts) . ')',
            'params' => $params,
        ];
    }

    public static function buildDistanceExpression(
        float $originLatitude,
        float $originLongitude,
        string $orderLatitudeColumn = 'o.x',
        string $orderLongitudeColumn = 'o.y'
    ): array {
        return [
            'sql' => "(
                6371 * ACOS(
                    LEAST(
                        1,
                        GREATEST(
                            -1,
                            COS(RADIANS(%f)) * COS(RADIANS({$orderLatitudeColumn})) * COS(RADIANS({$orderLongitudeColumn}) - RADIANS(%f)) +
                            SIN(RADIANS(%f)) * SIN(RADIANS({$orderLatitudeColumn}))
                        )
                    )
                )
            )",
            'params' => [$originLatitude, $originLongitude, $originLatitude],
        ];
    }

    public static function evaluateOrderMatch(
        array $territory,
        $orderZipcode,
        $orderLatitude,
        $orderLongitude
    ): array {
        $normalizedOrderZipcode = self::normalizeZipcode($orderZipcode);
        $zip_match = self::hasZipcode($territory)
            && $normalizedOrderZipcode !== null
            && $normalizedOrderZipcode === $territory['zipcode'];

        $distance_km = null;
        $radius_match = false;

        if (self::hasCircle($territory)) {
            $normalizedLatitude = self::normalizeCoordinate($orderLatitude);
            $normalizedLongitude = self::normalizeCoordinate($orderLongitude);

            if ($normalizedLatitude !== null && $normalizedLongitude !== null) {
                $distance_km = self::distanceKm(
                    (float) $territory['latitude'],
                    (float) $territory['longitude'],
                    $normalizedLatitude,
                    $normalizedLongitude
                );

                if ($distance_km !== null && $distance_km <= (float) $territory['radius_km']) {
                    $radius_match = true;
                }
            }
        }

        return [
            'matches' => $zip_match || $radius_match,
            'zip_match' => $zip_match,
            'radius_match' => $radius_match,
            'distance_km' => $distance_km,
        ];
    }

    public static function distanceKm(
        float $originLatitude,
        float $originLongitude,
        float $targetLatitude,
        float $targetLongitude
    ): ?float {
        $originLatRad = deg2rad($originLatitude);
        $originLngRad = deg2rad($originLongitude);
        $targetLatRad = deg2rad($targetLatitude);
        $targetLngRad = deg2rad($targetLongitude);

        $latDelta = $targetLatRad - $originLatRad;
        $lngDelta = $targetLngRad - $originLngRad;

        $a = sin($latDelta / 2) * sin($latDelta / 2)
            + cos($originLatRad) * cos($targetLatRad)
            * sin($lngDelta / 2) * sin($lngDelta / 2);

        $a = min(1, max(0, $a));
        $c = 2 * asin(sqrt($a));

        return 6371 * $c;
    }

    private static function normalizeZipcode($value): ?string
    {
        if ($value === null) {
            return null;
        }

        $zipcode = trim((string) $value);

        return $zipcode === '' ? null : $zipcode;
    }

    private static function normalizeCoordinate($value): ?float
    {
        if ($value === null || $value === '') {
            return null;
        }

        if (!is_numeric($value)) {
            return null;
        }

        return round((float) $value, 6);
    }
}
