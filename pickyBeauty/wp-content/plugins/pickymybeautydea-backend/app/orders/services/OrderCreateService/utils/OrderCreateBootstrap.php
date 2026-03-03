<?php
if (!defined('ABSPATH')) exit;

require_once __DIR__ . '/OrderCreateParseRequest.php';
require_once __DIR__ . '/OrderCreateSanitizeCoordinateValue.php';
require_once __DIR__ . '/OrderCreateSanitizeShippingInput.php';
require_once __DIR__ . '/OrderCreateResolveCoordinateValue.php';
require_once __DIR__ . '/OrderCreateInsertShipping.php';
require_once __DIR__ . '/OrderCreateFindOrCreateCustomer.php';
require_once __DIR__ . '/OrderCreateBuildOrderData.php';
require_once __DIR__ . '/OrderCreateInsertOrder.php';
require_once __DIR__ . '/OrderCreateResolveEmailRecipient.php';
require_once __DIR__ . '/OrderCreateBuildOrderEmailHtml.php';
require_once __DIR__ . '/OrderCreateSendConfirmationEmail.php';
require_once __DIR__ . '/OrderCreateBuildSuccessResponse.php';
require_once __DIR__ . '/OrderCreateHandle.php';
