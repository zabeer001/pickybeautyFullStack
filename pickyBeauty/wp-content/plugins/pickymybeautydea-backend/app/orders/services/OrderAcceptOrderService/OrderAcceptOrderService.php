<?php
if (!defined('ABSPATH')) exit;

require_once __DIR__ . '/utils/OrderAcceptOrderBootstrap.php';

class OrderAcceptOrderService
{
    public static function acceptOrder(\WP_REST_Request $request)
    {
        return OrderAcceptOrderHandle::run($request);
    }
}
