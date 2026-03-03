<?php
if (!defined('ABSPATH')) exit;

require_once __DIR__ . '/utils/OrderShowBootstrap.php';

class OrderShowService
{
    public static function show(\WP_REST_Request $request)
    {
        return OrderShowHandle::run($request);
    }
}
