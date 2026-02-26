<?php
if (!defined('ABSPATH')) exit;

require_once __DIR__ . '/services/StripeCreateIntentService.php';
require_once __DIR__ . '/services/StripeCreateCheckoutSessionService.php';
require_once __DIR__ . '/services/StripeCheckoutStatusService.php';
require_once __DIR__ . '/services/StripeWebhookService.php';

class StripeController
{
    public function create_intent(
        \WP_REST_Request $request
    ) {
        return StripeCreateIntentService::create_intent($request);
    }

    public function webhook(
        \WP_REST_Request $request
    ) {
        return StripeWebhookService::webhook($request);
    }

    public function create_checkout_session(
        \WP_REST_Request $request
    ) {
        return StripeCreateCheckoutSessionService::create_session($request);
    }

    public function checkout_status(
        \WP_REST_Request $request
    ) {
        return StripeCheckoutStatusService::check_status($request);
    }
}
