<?php
if (!defined('ABSPATH')) exit;

class OrderCreateResolveEmailRecipient
{
    public static function run(?array $shipping, int $user_id): string
    {
        if (!empty($shipping['email']) && is_email($shipping['email'])) {
            return $shipping['email'];
        }

        if ($user_id) {
            $user = get_user_by('id', $user_id);
            if ($user && is_email($user->user_email)) {
                return $user->user_email;
            }
        }

        return '';
    }
}
