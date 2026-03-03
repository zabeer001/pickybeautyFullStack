<?php

/**
 * Plugin Name: Pickymybeautydea Backend
 * Description: A react.js powered admin interface for WordPress.
 * Version: 1.0.0
 * Author: zabeer
 * Author URI: https://zabeer.dev
 * License: GPL2
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain: react-admin
 */

if (!defined('ABSPATH')) exit; // Prevent direct access

define('KIBSTERLP_API_KEY', 'asjkdkjasbdcj');

if (!defined('KIBSTERLP_STRIPE_PUBLISHABLE')) {
    $stripe_publishable = getenv('KIBSTERLP_STRIPE_PUBLISHABLE');
    if (!$stripe_publishable && isset($_ENV['KIBSTERLP_STRIPE_PUBLISHABLE'])) {
        $stripe_publishable = $_ENV['KIBSTERLP_STRIPE_PUBLISHABLE'];
    }
    if (!$stripe_publishable && isset($_SERVER['KIBSTERLP_STRIPE_PUBLISHABLE'])) {
        $stripe_publishable = $_SERVER['KIBSTERLP_STRIPE_PUBLISHABLE'];
    }
    if ($stripe_publishable) {
        define('KIBSTERLP_STRIPE_PUBLISHABLE', $stripe_publishable);
    }
}

if (!defined('KIBSTERLP_STRIPE_SECRET')) {
    $stripe_secret = getenv('KIBSTERLP_STRIPE_SECRET');
    if (!$stripe_secret && isset($_ENV['KIBSTERLP_STRIPE_SECRET'])) {
        $stripe_secret = $_ENV['KIBSTERLP_STRIPE_SECRET'];
    }
    if (!$stripe_secret && isset($_SERVER['KIBSTERLP_STRIPE_SECRET'])) {
        $stripe_secret = $_SERVER['KIBSTERLP_STRIPE_SECRET'];
    }
    if ($stripe_secret) {
        define('KIBSTERLP_STRIPE_SECRET', $stripe_secret);
    }
}

define('KIBSTERLP_ADMIN_PLUGIN', plugin_dir_path(__FILE__));






// // require_once REACT_ADMIN_PLUGIN . 'app/controller/signInController.php';

require_once KIBSTERLP_ADMIN_PLUGIN . 'app/routes/RestApiRoutes.php';
require_once KIBSTERLP_ADMIN_PLUGIN . 'app/email/GmailSmtp.php';
require_once KIBSTERLP_ADMIN_PLUGIN . 'app/migrations/createtables.php';
require_once KIBSTERLP_ADMIN_PLUGIN . 'app/migrations/MigrationRunner.php';
require_once KIBSTERLP_ADMIN_PLUGIN . 'app/migrations/TableHealth.php';
require_once KIBSTERLP_ADMIN_PLUGIN . 'app/user/ProfileFields.php';

register_activation_hook(__FILE__, function () {
    kibsterlp_run_migrations();
    kibsterlp_ensure_tables();
});

kibsterlp_register_table_health_hooks();


// register_deactivation_hook(__FILE__, function () {
//     // Intentionally keep data on deactivation.
// });

// // Load the main admin class
// require_once REACT_ADMIN_PLUGIN . 'app/react/React_Admin.php';
