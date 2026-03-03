<?php
if (!defined('ABSPATH')) exit;

class OrderCreateHandle
{
    public static function run(\WP_REST_Request $request)
    {
        $request_data = OrderCreateParseRequest::run($request);
        $shipping = OrderCreateSanitizeShippingInput::run($request_data['raw_shipping']);
        $x_value = OrderCreateResolveCoordinateValue::run($request, $shipping, 'x');
        $y_value = OrderCreateResolveCoordinateValue::run($request, $shipping, 'y');

        $shipping_id = $request_data['shipping_id'];
        if ($shipping) {
            $new_shipping_id = OrderCreateInsertShipping::run($shipping);
            if (is_wp_error($new_shipping_id)) {
                return $new_shipping_id;
            }
            $shipping_id = (int) $new_shipping_id;
        }

        $customer_id = OrderCreateFindOrCreateCustomer::run($shipping);
        $order_unique_id = wp_generate_uuid4();

        $data = OrderCreateBuildOrderData::run(
            $request_data,
            $customer_id,
            $shipping_id,
            $x_value,
            $y_value,
            $order_unique_id
        );

        $id = OrderCreateInsertOrder::run($data);
        if (is_wp_error($id)) {
            return $id;
        }

        $email_result = OrderCreateSendConfirmationEmail::run(
            $id,
            $order_unique_id,
            $request_data['order_title'],
            $shipping,
            $request_data['user_id']
        );

        return OrderCreateBuildSuccessResponse::run($id, $order_unique_id, $shipping_id, $email_result);
    }
}
