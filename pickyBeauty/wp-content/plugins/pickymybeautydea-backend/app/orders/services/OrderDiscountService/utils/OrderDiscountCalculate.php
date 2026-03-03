<?php
if (!defined('ABSPATH')) exit;

class OrderDiscountCalculate
{
    public static function run(float $budget, array $customer, array $loyalty_rule): array
    {
        $order_complete_count = isset($customer['order_complete_count']) ? (int) $customer['order_complete_count'] : 0;
        $order_complete_percentage = isset($customer['order_complete_percentage']) ? (int) $customer['order_complete_percentage'] : 0;

        $discount_percentage = isset($loyalty_rule['discount']) ? (int) $loyalty_rule['discount'] : 0;
        if ($discount_percentage < 0) {
            $discount_percentage = 0;
        }

        $discount_amount = ($budget * $discount_percentage) / 100;
        $discounted_budget = max(0, $budget - $discount_amount);

        return [
            'discount_percentage' => $discount_percentage,
            'discounted_budget' => round($discounted_budget, 2),
            'order_complete_count' => $order_complete_count,
            'order_complete_percentage' => $order_complete_percentage,
        ];
    }
}
