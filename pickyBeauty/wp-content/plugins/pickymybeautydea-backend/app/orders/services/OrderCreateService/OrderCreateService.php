<?php
if (!defined('ABSPATH')) exit;

require_once __DIR__ . '/utils/OrderCreateBootstrap.php';

class OrderCreateService
{
    public static function create(\WP_REST_Request $request)
    {
        return OrderCreateHandle::run($request);
    }
}
