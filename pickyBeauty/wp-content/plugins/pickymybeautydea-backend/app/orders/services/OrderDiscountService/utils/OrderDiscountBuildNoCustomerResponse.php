<?php
if (!defined('ABSPATH')) exit;

class OrderDiscountBuildNoCustomerResponse
{
    public static function run(string $email, float $budget): \WP_REST_Response
    {
        return new \WP_REST_Response([
            'success' => true,
            'message' => 'No customer found for the provided email. No discount applied.',
            'status' => 200,
            'data' => [
                'discount_percentage' => 0,
                'user_email' => $email,
                'original_budget' => $budget,
                'discounted_budget' => $budget,
            ],
        ], 200);
    }
}
