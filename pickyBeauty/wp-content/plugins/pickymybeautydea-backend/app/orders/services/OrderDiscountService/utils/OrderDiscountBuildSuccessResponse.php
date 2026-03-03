<?php
if (!defined('ABSPATH')) exit;

class OrderDiscountBuildSuccessResponse
{
    public static function run(string $email, float $budget, array $calculation, array $loyalty_rule): \WP_REST_Response
    {
        return new \WP_REST_Response([
            'success' => true,
            'message' => 'Discount calculated successfully.',
            'status' => 200,
            'data' => [
                'discount_percentage' => $calculation['discount_percentage'],
                'user_email' => $email,
                'original_budget' => $budget,
                'discounted_budget' => $calculation['discounted_budget'],
                'customer' => [
                    'order_complete_count' => $calculation['order_complete_count'],
                    'order_complete_percentage' => $calculation['order_complete_percentage'],
                ],
                'loyalty_rule' => [
                    'id' => isset($loyalty_rule['id']) ? (int) $loyalty_rule['id'] : null,
                    'min_order' => isset($loyalty_rule['min_order']) ? (int) $loyalty_rule['min_order'] : null,
                    'max_order' => isset($loyalty_rule['max_order']) ? (int) $loyalty_rule['max_order'] : null,
                    'required_order_complete_percentage' => isset($loyalty_rule['order_complete_percentage']) ? (int) $loyalty_rule['order_complete_percentage'] : null,
                ],
            ],
        ], 200);
    }
}
