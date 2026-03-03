<?php
if (!defined('ABSPATH')) exit;

class OrderAcceptOrderHandle
{
    public static function run(\WP_REST_Request $request): \WP_REST_Response
    {
        global $wpdb;

        $request_data = OrderAcceptOrderParseRequest::run($request);
        $current_user = OrderAcceptOrderEnsureVendorUser::run();
        if ($current_user instanceof \WP_REST_Response) return $current_user;

        $territory = OrderVendorTerritoryService::getVendorTerritory($current_user->ID);
        $territory_error = OrderAcceptOrderEnsureVendorTerritory::run($territory);
        if ($territory_error instanceof \WP_REST_Response) return $territory_error;

        $order = OrderAcceptOrderFindOrder::run(
            $wpdb,
            $wpdb->prefix . 'kib_orders',
            $wpdb->prefix . 'kib_shipping_addresses',
            $request_data['order_unique_id']
        );

        if (!$order) return OrderAcceptOrderBuildOrderNotFoundResponse::run();

        $territory_match = OrderAcceptOrderEvaluateTerritory::run($territory, $order);
        if (!$territory_match['matches']) {
            return OrderAcceptOrderBuildTerritoryMismatchResponse::run($territory, $order, $territory_match);
        }

        if (OrderAcceptOrderIsAlreadyAccepted::run($order)) {
            return OrderAcceptOrderBuildAlreadyAcceptedResponse::run();
        }

        $updated = OrderAcceptOrderPersistAcceptance::run(
            $wpdb,
            $wpdb->prefix . 'kib_orders',
            $request_data['order_unique_id'],
            $request_data['normalized_status'],
            $current_user->ID
        );

        if ($updated === false) return OrderAcceptOrderBuildDatabaseErrorResponse::run();

        $email_result = OrderAcceptOrderSendAcceptedEmail::run(
            (int) $order['id'],
            $request_data['order_unique_id'],
            $order['order_title'] ?? null,
            $order['email'] ?? null,
            $order['name'] ?? null,
            isset($order['user_id']) ? (int) $order['user_id'] : 0
        );

        return OrderAcceptOrderBuildSuccessResponse::run(
            $request_data['order_unique_id'],
            $current_user->ID,
            $request_data['normalized_status'],
            $territory,
            $territory_match,
            $email_result
        );
    }
}
