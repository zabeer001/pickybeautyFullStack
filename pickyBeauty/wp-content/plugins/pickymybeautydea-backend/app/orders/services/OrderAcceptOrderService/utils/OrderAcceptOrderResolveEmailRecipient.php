<?php
if (!defined('ABSPATH')) exit;

class OrderAcceptOrderResolveEmailRecipient
{
    public static function run(?string $shipping_email, ?string $shipping_name, int $user_id): array
    {
        if (!empty($shipping_email) && is_email($shipping_email)) {
            return [
                'to' => $shipping_email,
                'name' => $shipping_name ? $shipping_name : '',
            ];
        }

        if ($user_id) {
            $user = get_user_by('id', $user_id);
            if ($user && is_email($user->user_email)) {
                return [
                    'to' => $user->user_email,
                    'name' => $user->display_name ? $user->display_name : '',
                ];
            }
        }

        return [
            'to' => '',
            'name' => '',
        ];
    }
}
