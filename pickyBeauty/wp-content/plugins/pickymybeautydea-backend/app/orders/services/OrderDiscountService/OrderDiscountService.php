<?php
if (!defined('ABSPATH')) exit;

require_once __DIR__ . '/utils/OrderDiscountBootstrap.php';

class OrderDiscountService
{
    public static function orderDiscount(\WP_REST_Request $request)
    {
        return OrderDiscountHandle::run($request);
    }
}
