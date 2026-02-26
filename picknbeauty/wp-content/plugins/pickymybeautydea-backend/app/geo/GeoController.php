<?php
if (!defined('ABSPATH')) exit;

require_once __DIR__ . '/services/GeoReverseService.php';

class GeoController
{
    public function reverse(\WP_REST_Request $request)
    {
        return GeoReverseService::reverse($request);
    }
}
