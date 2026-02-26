<?php
if (!defined('ABSPATH')) exit;
require_once __DIR__ . '/services/CustomerIndexService.php';
require_once __DIR__ . '/services/CustomerCreateService.php';
require_once __DIR__ . '/services/CustomerShowService.php';
require_once __DIR__ . '/services/CustomerUpdateService.php';
require_once __DIR__ . '/services/CustomerDeleteService.php';

class CustomerController
{
    public function index(\WP_REST_Request $request)
    {
        return CustomerIndexService::index($request);
    }

    public function create(\WP_REST_Request $request)
    {
        return CustomerCreateService::create($request);
    }

    public function show(\WP_REST_Request $request)
    {
        return CustomerShowService::show($request);
    }

    public function update(\WP_REST_Request $request)
    {
        return CustomerUpdateService::update($request);
    }

    public function delete(\WP_REST_Request $request)
    {
        return CustomerDeleteService::delete($request);
    }
}
