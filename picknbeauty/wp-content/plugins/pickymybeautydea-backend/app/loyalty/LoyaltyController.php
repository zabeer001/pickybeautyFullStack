<?php
if (!defined('ABSPATH')) {
    exit;
}
require_once __DIR__ . '/services/LoyaltyIndexService.php';
require_once __DIR__ . '/services/LoyaltyShowService.php';
require_once __DIR__ . '/services/LoyaltyCreateService.php';
require_once __DIR__ . '/services/LoyaltyUpdateService.php';
require_once __DIR__ . '/services/LoyaltyDeleteService.php';

class LoyaltyController
{
    public function index(\WP_REST_Request $request)
    {
        return LoyaltyIndexService::index($request);
    }

    public function show(\WP_REST_Request $request)
    {
        return LoyaltyShowService::show($request);
    }

    public function create(\WP_REST_Request $request)
    {
        return LoyaltyCreateService::create($request);
    }

    public function update(\WP_REST_Request $request)
    {
        return LoyaltyUpdateService::update($request);
    }

    public function delete(\WP_REST_Request $request)
    {
        return LoyaltyDeleteService::delete($request);
    }
}
