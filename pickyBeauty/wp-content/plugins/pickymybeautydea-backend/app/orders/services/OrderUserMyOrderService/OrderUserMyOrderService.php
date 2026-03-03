<?php
if (!defined('ABSPATH')) exit;

require_once __DIR__ . '/utils/OrderUserMyOrderBootstrap.php';

class OrderUserMyOrderService
{
    public static function userMyOrder(\WP_REST_Request $request)
    {
        return OrderUserMyOrderHandle::run($request);
    }
}
