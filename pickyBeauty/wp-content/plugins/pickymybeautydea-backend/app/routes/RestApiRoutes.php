<?php
if (!defined('ABSPATH')) exit;

require_once __DIR__ . '/../categories/CategoryRoutes.php';
require_once __DIR__ . '/../customers/CustomerRoutes.php';
require_once __DIR__ . '/../orders/OrderRoutes.php';
require_once __DIR__ . '/../geo/GeoRoutes.php';
require_once __DIR__ . '/../payments/StripeRoutes.php';
require_once __DIR__ . '/../user/UserRoutes.php';
require_once __DIR__ . '/../loyalty/LoyaltyRoutes.php';
require_once __DIR__ . '/../email/TestEmailRoutes.php';

class RestApiRoutes
{
    private CategoryRoutes $categoryRoutes;
    private CustomerRoutes $customerRoutes;
    private OrderRoutes $orderRoutes;
    private GeoRoutes $geoRoutes;
    private StripeRoutes $stripeRoutes;
    private UserRoutes $userRoutes;
    private LoyaltyRoutes $loyaltyRoutes;
    private TestEmailRoutes $testEmailRoutes;

    public function __construct()
    {
        $this->categoryRoutes = new CategoryRoutes();
        $this->customerRoutes = new CustomerRoutes();
        $this->orderRoutes    = new OrderRoutes();
        $this->geoRoutes      = new GeoRoutes();
        $this->stripeRoutes   = new StripeRoutes();
        $this->userRoutes     = new UserRoutes();
        $this->loyaltyRoutes  = new LoyaltyRoutes();
        $this->testEmailRoutes = new TestEmailRoutes();

        add_action('rest_api_init', [$this, 'register_routes']);
        add_action('init', [$this, 'flush_routes_once']); // TEMP
    }

    public function register_routes()
    {
        // /categories
        $this->categoryRoutes->register_routes();

        // /customers
        $this->customerRoutes->register_routes();

        // /orders /my-orders
        $this->orderRoutes->register_routes();

        // /geo
        $this->geoRoutes->register_routes();

        // /stripe
        $this->stripeRoutes->register_routes();

        // loyalty
        $this->loyaltyRoutes->register_routes();

        // users
        $this->userRoutes->register_routes();

        // test email
        $this->testEmailRoutes->register_routes();


    }

    // TEMPORARY: flush old routes once
    public function flush_routes_once()
    {
        global $wp_rest_server;
        if (isset($wp_rest_server)) {
            $wp_rest_server->flush_routes();
        }
        remove_action('init', [$this, 'flush_routes_once']);
    }
}

if (class_exists('RestApiRoutes')) {
    new RestApiRoutes();
}
