<?php
if (!defined('ABSPATH')) exit;

class OrderUserMyOrderResolveUserEmail
{
    public static function run($current_user): string
    {
        return isset($current_user->user_email) ? sanitize_email($current_user->user_email) : '';
    }
}
