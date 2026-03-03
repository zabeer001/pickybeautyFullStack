<?php
if (!defined('ABSPATH')) exit;

class OrderUpdateSanitizeShippingInput
{
    public static function run($payload): ?array
    {
        if (!is_array($payload)) {
            return null;
        }

        return [
            'email' => isset($payload['email']) ? sanitize_text_field($payload['email']) : null,
            'phone' => isset($payload['phone']) ? sanitize_text_field($payload['phone']) : null,
            'name' => isset($payload['name']) ? sanitize_text_field($payload['name']) : null,
            'country' => isset($payload['country']) ? sanitize_text_field($payload['country']) : null,
            'city' => isset($payload['city']) ? sanitize_text_field($payload['city']) : null,
            'district' => isset($payload['district']) ? sanitize_text_field($payload['district']) : null,
            'zip_code' => isset($payload['zip_code']) ? sanitize_text_field($payload['zip_code']) : null,
        ];
    }
}
