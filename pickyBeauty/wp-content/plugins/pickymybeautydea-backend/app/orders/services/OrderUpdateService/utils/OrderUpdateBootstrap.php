<?php
if (!defined('ABSPATH')) exit;

require_once __DIR__ . '/OrderUpdateParseRequest.php';
require_once __DIR__ . '/OrderUpdateBuildPayload.php';
require_once __DIR__ . '/OrderUpdateResolveOrderEmail.php';
require_once __DIR__ . '/OrderUpdateCustomerOrderStats.php';
require_once __DIR__ . '/OrderUpdateCustomerOrderCompleteCount.php';
require_once __DIR__ . '/OrderUpdateCustomerOrderCancelledCount.php';
require_once __DIR__ . '/OrderUpdateHandlePaymentStatusTransition.php';
require_once __DIR__ . '/OrderUpdateHandleSharingStatusTransition.php';
require_once __DIR__ . '/OrderUpdateSanitizeShippingInput.php';
require_once __DIR__ . '/OrderUpdateInsertShipping.php';
require_once __DIR__ . '/OrderUpdateApplyShippingChanges.php';
require_once __DIR__ . '/OrderUpdateBuildNoChangesError.php';
require_once __DIR__ . '/OrderUpdatePersist.php';
require_once __DIR__ . '/OrderUpdateBuildUpdateFailedError.php';
require_once __DIR__ . '/OrderUpdateBuildSuccessResponse.php';
require_once __DIR__ . '/OrderUpdateHandle.php';
