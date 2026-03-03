<?php
if (!defined('ABSPATH')) exit;

class OrderDiscountHandle
{
    public static function run(\WP_REST_Request $request): \WP_REST_Response
    {
        global $wpdb;

        $request_data = OrderDiscountParseRequest::run($request);
        $validation_error = OrderDiscountValidateRequest::run($request_data);
        if ($validation_error) {
            return OrderDiscountBuildValidationErrorResponse::run($validation_error);
        }

        $customer = OrderDiscountFindCustomer::run($wpdb, $request_data['email']);
        if (!$customer) {
            return OrderDiscountBuildNoCustomerResponse::run($request_data['email'], $request_data['budget']);
        }

        $order_complete_count = isset($customer['order_complete_count']) ? (int) $customer['order_complete_count'] : 0;
        $order_complete_percentage = isset($customer['order_complete_percentage']) ? (int) $customer['order_complete_percentage'] : 0;
        $loyalty_rule = OrderDiscountFindLoyaltyRule::run($wpdb, $order_complete_count, $order_complete_percentage);
        if (!$loyalty_rule) {
            return OrderDiscountBuildNoRuleResponse::run($request_data['email'], $request_data['budget']);
        }

        $calculation = OrderDiscountCalculate::run($request_data['budget'], $customer, $loyalty_rule);

        return OrderDiscountBuildSuccessResponse::run(
            $request_data['email'],
            $request_data['budget'],
            $calculation,
            $loyalty_rule
        );
    }
}
