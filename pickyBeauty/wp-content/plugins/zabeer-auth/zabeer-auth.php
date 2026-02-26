<?php // PHP entrypoint for the plugin.
/**
 * Plugin Name: Zabeer Auth // WordPress plugin header: display name.
 * Description: A react.js powered admin interface for WordPress. Adds 'vendor' role and creates pages via controller. // Short plugin summary.
 * Version: 1.1.1 // Plugin version.
 * Author: zabeer // Plugin author.
 * Author URI: https://zabeer.dev // Author website.
 * License: GPL2 // License identifier.
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html // License text URL.
 * Text Domain: react-admin // Translation domain.
 */

/*
 * Zabeer Auth plugin bootstrap file.
 *
 * This file stays intentionally small and only wires modules together. The goal
 * is to keep the entrypoint clean while the behavior lives in focused files:
 *
 * - app/class/PageCreationController.php
 *   Creates/removes the Sign In and Register pages and registers shortcodes.
 *
 * - app/class/RoleCreationController.php
 *   Adds/removes the custom vendor role on activation/deactivation.
 *
 * - app/class/ExtraInfoController.php
 *   Handles any extra user fields that are not part of core registration.
 *
 * - app/helpers/Avatar.php
 *   Uploads vendor logo/avatar files and overrides avatar URL lookups.
 *
 * - app/helpers/Email.php
 *   Sends welcome emails for vendor and customer registrations.
 *
 * - app/rest/RestController.php
 *   Registers REST endpoints (e.g., /me and /register) and processes requests.
 *
 * - app/Bootstrap.php
 *   Central place to attach hooks/filters and initialize REST routes.
 */
if (!defined('ABSPATH')) exit; // Prevent direct access from the web.

// Define plugin path. // Path is reused for all includes.
define('ZABEER_AUTH_PATH', plugin_dir_path(__FILE__)); // Filesystem path to this plugin.

// Load controllers, helpers, and the main bootstrap. // Keeps the entry file minimal.
require_once ZABEER_AUTH_PATH . 'app/class/PageCreationController.php'; // Creates pages and registers shortcodes.
require_once ZABEER_AUTH_PATH . 'app/class/RoleCreationController.php'; // Adds/removes the vendor role.
require_once ZABEER_AUTH_PATH . 'app/class/ExtraInfoController.php'; // Adds vendor profile fields in wp-admin.

require_once ZABEER_AUTH_PATH . 'app/helpers/Avatar.php'; // Avatar upload and avatar URL filter.
require_once ZABEER_AUTH_PATH . 'app/helpers/Email.php'; // Welcome email templates and sender.
require_once ZABEER_AUTH_PATH . 'app/rest/RestController.php'; // REST endpoints for auth data.
require_once ZABEER_AUTH_PATH . 'app/Bootstrap.php'; // Hook registration and plugin initialization.

// Activation/deactivation hooks for roles and pages. // Keep side-effects in lifecycle events.
register_activation_hook(__FILE__, ['RoleCreationController', 'add_role']); // Create vendor role.
register_activation_hook(__FILE__, ['PageCreationController', 'zabeer_auth_create_page']); // Create auth pages.

register_deactivation_hook(__FILE__, ['PageCreationController', 'zabeer_auth_remove_page']); // Remove auth pages.
register_deactivation_hook(__FILE__, ['RoleCreationController', 'remove_role']); // Remove vendor role.

// Boot the plugin (hooks, filters, and REST routes). // Starts runtime behavior.
ZabeerAuthBootstrap::init(); // Initialize plugin hooks and REST routes.
