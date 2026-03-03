<?php
if (!defined('ABSPATH')) exit;

class OrderUpdateHandle
{
    public static function run(\WP_REST_Request $request)
    {
        global $wpdb;

        $orders_table = $wpdb->prefix . 'kib_orders';
        $request_data = OrderUpdateParseRequest::run($request);
        $payload = OrderUpdateBuildPayload::run($request);

        $email = OrderUpdateResolveOrderEmail::run(
            $wpdb,
            $orders_table,
            $wpdb->prefix . 'kib_customers',
            $wpdb->prefix . 'kib_shipping_addresses',
            $request_data['id']
        );

        $payment_transition = OrderUpdateHandlePaymentStatusTransition::run(
            $wpdb,
            $orders_table,
            $request_data['id'],
            $payload['data'],
            $email
        );

        $sharing_transition = OrderUpdateHandleSharingStatusTransition::run(
            $wpdb,
            $orders_table,
            $request_data['id'],
            $payload['data'],
            $email
        );

        $shipping = OrderUpdateSanitizeShippingInput::run($request_data['shipping']);
        $payload = OrderUpdateApplyShippingChanges::run(
            $wpdb,
            $orders_table,
            $request_data['id'],
            $shipping,
            $payload
        );

        if (isset($payload['error']) && is_wp_error($payload['error'])) {
            return $payload['error'];
        }

        if (empty($payload['data'])) {
            return OrderUpdateBuildNoChangesError::run();
        }

        $updated = OrderUpdatePersist::run(
            $wpdb,
            $orders_table,
            $request_data['id'],
            $payload['data'],
            $payload['types']
        );

        if ($updated === false) {
            return OrderUpdateBuildUpdateFailedError::run();
        }

        return OrderUpdateBuildSuccessResponse::run(
            (int) $updated,
            $request_data['id'],
            $payment_transition['payment_status_compare'],
            $sharing_transition['sharing_status_compare'],
            $email,
            $payment_transition['customer_updated'],
            $sharing_transition['customer_cancel_updated']
        );
    }
}
