<?php
if (!defined('ABSPATH')) exit;

class OrderUpdateCustomerOrderCancelledCount
{
    public static function run($email): bool
    {
        return OrderUpdateCustomerOrderStats::run($email, 0, 1);
    }
}
