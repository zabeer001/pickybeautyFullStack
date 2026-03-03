<?php
if (!defined('ABSPATH')) exit;

class OrderAcceptOrderIsAlreadyAccepted
{
    public static function run(array $order): bool
    {
        return strtolower((string) ($order['sharing_status'] ?? '')) === 'accepted';
    }
}
