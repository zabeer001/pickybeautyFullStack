<?php
if (!defined('ABSPATH')) exit;

class OrderDiscountValidateRequest
{
    public static function run(array $request_data): ?array
    {
        if (empty($request_data['email']) || empty($request_data['budget'])) {
            return [
                'message' => 'Missing required parameters: email and budget are necessary.',
                'status' => 400,
            ];
        }

        if (!is_email($request_data['email'])) {
            return [
                'message' => 'Invalid email address.',
                'status' => 400,
            ];
        }

        if ($request_data['budget'] < 0) {
            return [
                'message' => 'Budget must be zero or greater.',
                'status' => 400,
            ];
        }

        return null;
    }
}
