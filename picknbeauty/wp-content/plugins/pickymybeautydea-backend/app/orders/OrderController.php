<?php
if (!defined('ABSPATH')) exit;
require_once __DIR__ . '/services/OrderIndexService.php';
require_once __DIR__ . '/services/OrderCreateService.php';
require_once __DIR__ . '/services/OrderShowService.php';
require_once __DIR__ . '/services/OrderUpdateService.php';
require_once __DIR__ . '/services/OrderAcceptOrderService.php';
require_once __DIR__ . '/services/OrderUpdatePaymentStatusService.php';
require_once __DIR__ . '/services/OrderDeleteService.php';
require_once __DIR__ . '/services/OrderDiscountService.php';
require_once __DIR__ . '/services/OrderVendorMyOrderService.php';
require_once __DIR__ . '/services/OrderUserMyOrderService.php';

class OrderController
{
    public function index(\WP_REST_Request $request)
    {
        return OrderIndexService::index($request);
    }

    public function create(\WP_REST_Request $request)
    {
        return OrderCreateService::create($request);
    }

    public function show(\WP_REST_Request $request)
    {
        return OrderShowService::show($request);
    }

    public function update(\WP_REST_Request $request)
    {
        return OrderUpdateService::update($request);
    }

    public function acceptOrder(\WP_REST_Request $request)
    {
        return OrderAcceptOrderService::acceptOrder($request);
    }

    public function update_payment_status(\WP_REST_Request $request)
    {
        return OrderUpdatePaymentStatusService::update_payment_status($request);
    }

    public function delete(\WP_REST_Request $request)
    {
        return OrderDeleteService::delete($request);
    }

    public function orderDiscount(\WP_REST_Request $request)
    {
        return OrderDiscountService::orderDiscount($request);
    }

    public function myOrder(\WP_REST_Request $request)
    {
        return OrderVendorMyOrderService::vendorMyOrder($request);
    }

    public function userMyOrder(\WP_REST_Request $request)
    {
        return OrderUserMyOrderService::userMyOrder($request);
    }
}
