<?php
if (!defined('ABSPATH')) exit;

require_once __DIR__ . '/utils/OrderUpdateBootstrap.php';

class OrderUpdateService
{
    public static function update(\WP_REST_Request $request)
    {
        return OrderUpdateHandle::run($request);
    }
}
