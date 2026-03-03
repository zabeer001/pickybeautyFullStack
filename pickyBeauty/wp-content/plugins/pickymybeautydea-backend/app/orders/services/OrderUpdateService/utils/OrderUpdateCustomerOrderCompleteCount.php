<?php
if (!defined('ABSPATH')) exit;

class OrderUpdateCustomerOrderCompleteCount
{
    public static function run($email): bool
    {
        return OrderUpdateCustomerOrderStats::run($email, 1, 0);
    }
}
