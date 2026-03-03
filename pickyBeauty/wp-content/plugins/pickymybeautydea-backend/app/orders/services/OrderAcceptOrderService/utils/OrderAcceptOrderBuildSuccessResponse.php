<?php
if (!defined('ABSPATH')) exit;

class OrderAcceptOrderBuildSuccessResponse
{
    public static function run(
        string $order_unique_id,
        int $user_id,
        string $normalized_status,
        array $territory,
        array $territory_match,
        array $email_result
    ): \WP_REST_Response {
        return new \WP_REST_Response([
            'status' => true,
            'message' => 'Order accepted successfully.',
            'data' => [
                'order_unique_id' => $order_unique_id,
                'vendor_id' => $user_id,
                'sharing_status' => $normalized_status,
                'zip_code' => $territory['zipcode'],
                'territory_match' => [
                    'zip_match' => $territory_match['zip_match'],
                    'radius_match' => $territory_match['radius_match'],
                    'distance_km' => $territory_match['distance_km'] !== null
                        ? round((float) $territory_match['distance_km'], 2)
                        : null,
                ],
                'email' => $email_result,
            ],
        ], 200);
    }
}
