<?php
if (!defined('ABSPATH')) exit;
require_once __DIR__ . '/services/CategoryIndexService.php';
require_once __DIR__ . '/services/CategoryCreateService.php';
require_once __DIR__ . '/services/CategoryShowService.php';
require_once __DIR__ . '/services/CategoryUpdateService.php';
require_once __DIR__ . '/services/CategoryDeleteService.php';

class CategoryController
{
    public function index(\WP_REST_Request $request)
    {
        return CategoryIndexService::index($request);
    }

    public function create(\WP_REST_Request $request)
    {
        return CategoryCreateService::create($request);
    }

    public function show(\WP_REST_Request $request)
    {
        return CategoryShowService::show($request);
    }

    public function update(\WP_REST_Request $request)
    {
        return CategoryUpdateService::update($request);
    }

    public function delete(\WP_REST_Request $request)
    {
        return CategoryDeleteService::delete($request);
    }
}
