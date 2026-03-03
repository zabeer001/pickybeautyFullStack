<?php
if (!defined('ABSPATH')) exit;

require_once __DIR__ . '/OrderDiscountParseRequest.php';
require_once __DIR__ . '/OrderDiscountValidateRequest.php';
require_once __DIR__ . '/OrderDiscountBuildValidationErrorResponse.php';
require_once __DIR__ . '/OrderDiscountFindCustomer.php';
require_once __DIR__ . '/OrderDiscountBuildNoCustomerResponse.php';
require_once __DIR__ . '/OrderDiscountFindLoyaltyRule.php';
require_once __DIR__ . '/OrderDiscountBuildNoRuleResponse.php';
require_once __DIR__ . '/OrderDiscountCalculate.php';
require_once __DIR__ . '/OrderDiscountBuildSuccessResponse.php';
require_once __DIR__ . '/OrderDiscountHandle.php';
