<?php
if (!defined('ABSPATH')) exit;

class OrderDiscountService
{
    public static function orderDiscount(\WP_REST_Request $request)
    {
        global $wpdb;

        $email  = sanitize_email($request->get_param('email'));
        $budget = (float) $request->get_param('budget');

        if (empty($email) || empty($budget)) {
            return new \WP_REST_Response([
                'success' => false,
                'message' => 'Missing required parameters: email and budget are necessary.',
                'status'  => 400,
            ], 400);
        }

        if (!is_email($email)) {
            return new \WP_REST_Response([
                'success' => false,
                'message' => 'Invalid email address.',
                'status'  => 400,
            ], 400);
        }

        if ($budget < 0) {
            return new \WP_REST_Response([
                'success' => false,
                'message' => 'Budget must be zero or greater.',
                'status'  => 400,
            ], 400);
        }

        $customers_table = $wpdb->prefix . 'kib_customers';
        $loyalty_table   = $wpdb->prefix . 'kib_loyalty';

        // 1) Find customer by email
        $customer = $wpdb->get_row(
            $wpdb->prepare("SELECT * FROM {$customers_table} WHERE email = %s", $email),
            ARRAY_A
        );

        if (!$customer) {
            // No customer found: return zero discount and unchanged budget
            return new \WP_REST_Response([
                'success' => true,
                'message' => 'No customer found for the provided email. No discount applied.',
                'status'  => 200,
                'data'    => [
                    'discount_percentage' => 0,
                    'user_email'          => $email,
                    'original_budget'     => $budget,
                    'discounted_budget'   => $budget,
                ],
            ], 200);
        }

        $order_complete_count       = isset($customer['order_complete_count']) ? (int) $customer['order_complete_count'] : 0;
        $order_complete_percentage  = isset($customer['order_complete_percentage']) ? (int) $customer['order_complete_percentage'] : 0;

        // 2) Find applicable loyalty rule:
        //    - customer completed orders between min_order and max_order (inclusive)
        //    - customer completion % >= rule.order_complete_percentage
        //    - rule status is active
        $loyalty_rule = $wpdb->get_row(
            $wpdb->prepare(
                "SELECT * FROM {$loyalty_table}
                 WHERE status = %s
                   AND %d >= min_order
                   AND %d <= max_order
                   AND %d >= order_complete_percentage
                 ORDER BY discount DESC, id ASC
                 LIMIT 1",
                'active',
                $order_complete_count,
                $order_complete_count,
                $order_complete_percentage
            ),
            ARRAY_A
        );

        if (!$loyalty_rule) {
            return new \WP_REST_Response([
                'success' => true,
                'message' => 'No applicable loyalty discount for this customer.',
                'status'  => 200,
                'data'    => [
                    'discount_percentage' => 0,
                    'user_email'          => $email,
                    'original_budget'     => $budget,
                    'discounted_budget'   => $budget,
                ],
            ], 200);
        }

        $discount_percentage = isset($loyalty_rule['discount']) ? (int) $loyalty_rule['discount'] : 0;
        if ($discount_percentage < 0) {
            $discount_percentage = 0;
        }

        $discount_amount   = ($budget * $discount_percentage) / 100;
        $discounted_budget = max(0, $budget - $discount_amount);

        return new \WP_REST_Response([
            'success' => true,
            'message' => 'Discount calculated successfully.',
            'status'  => 200,
            'data'    => [
                'discount_percentage' => $discount_percentage,
                'user_email'          => $email,
                'original_budget'     => $budget,
                'discounted_budget'   => round($discounted_budget, 2),
                'customer'            => [
                    'order_complete_count'      => $order_complete_count,
                    'order_complete_percentage' => $order_complete_percentage,
                ],
                'loyalty_rule'        => [
                    'id'                        => isset($loyalty_rule['id']) ? (int) $loyalty_rule['id'] : null,
                    'min_order'                 => isset($loyalty_rule['min_order']) ? (int) $loyalty_rule['min_order'] : null,
                    'max_order'                 => isset($loyalty_rule['max_order']) ? (int) $loyalty_rule['max_order'] : null,
                    'required_order_complete_percentage' => isset($loyalty_rule['order_complete_percentage']) ? (int) $loyalty_rule['order_complete_percentage'] : null,
                ],
            ],
        ], 200);
    }
}
