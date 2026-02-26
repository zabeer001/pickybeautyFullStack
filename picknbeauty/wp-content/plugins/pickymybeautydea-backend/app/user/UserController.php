<?php
if (!defined('ABSPATH')) exit;
require_once __DIR__ . '/services/UserIndexService.php';
require_once __DIR__ . '/services/UserMeService.php';
require_once __DIR__ . '/services/UserLocationService.php';
require_once __DIR__ . '/services/UserLocationStatusService.php';
require_once __DIR__ . '/services/UserLocationGetService.php';

class UserController
{
    public function index(\WP_REST_Request $request)
    {
        return UserIndexService::index($request);
    }

    public function me(\WP_REST_Request $request)
    {
        return UserMeService::me($request);
    }

    public function save_location(\WP_REST_Request $request)
    {
        return UserLocationService::save($request);
    }

    public function update_location_status(\WP_REST_Request $request)
    {
        return UserLocationStatusService::update($request);
    }

    public function get_location(\WP_REST_Request $request)
    {
        return UserLocationGetService::get($request);
    }
}
