<?php
if (!defined('ABSPATH')) exit;

require_once __DIR__ . '/utils/OrderVendorMyOrderBootstrap.php';

class OrderVendorMyOrderService
{
    public static function vendorMyOrder(\WP_REST_Request $request)
    {
        return OrderVendorMyOrderHandle::run($request);
    }
}
