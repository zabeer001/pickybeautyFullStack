<?php
if (!defined('ABSPATH')) exit;
require_once __DIR__ . '/services/OrderIndexService/OrderIndexService.php';
require_once __DIR__ . '/services/OrderCreateService/OrderCreateService.php';
require_once __DIR__ . '/services/OrderShowService/OrderShowService.php';
require_once __DIR__ . '/services/OrderUpdateService/OrderUpdateService.php';
require_once __DIR__ . '/services/OrderAcceptOrderService/OrderAcceptOrderService.php';
require_once __DIR__ . '/services/OrderUpdatePaymentStatusService/OrderUpdatePaymentStatusService.php';
require_once __DIR__ . '/services/OrderDeleteService/OrderDeleteService.php';
require_once __DIR__ . '/services/OrderDiscountService/OrderDiscountService.php';
require_once __DIR__ . '/services/OrderVendorMyOrderService/OrderVendorMyOrderService.php';
require_once __DIR__ . '/services/OrderUserMyOrderService/OrderUserMyOrderService.php';

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
