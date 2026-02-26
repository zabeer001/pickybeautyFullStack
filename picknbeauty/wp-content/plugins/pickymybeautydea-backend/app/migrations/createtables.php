<?php

/**
 * Kibsterlp Backend – DB migrations
 * Location: app/migrations/createtables.php
 */

namespace Kibsterlp\App\Migrations;

use Kibsterlp\App\Categories\Migrations\CategoryMigration;
use Kibsterlp\App\Customers\Migrations\CustomerMigration;
use Kibsterlp\App\Loyalty\Migrations\LoyaltyMigration;
use Kibsterlp\App\Orders\Migrations\OrderMigration;
use Kibsterlp\App\User\Migrations\XyMigration;

if (!defined('ABSPATH')) {
    exit;
}

require_once __DIR__ . '/../categories/migrations/2024_12_20_000001_create_categories_table.php';
require_once __DIR__ . '/../customers/migrations/2024_12_20_000002_create_customers_table.php';
require_once __DIR__ . '/../loyalty/migrations/2024_12_20_000003_create_loyalty_table.php';
require_once __DIR__ . '/../orders/migrations/2024_12_20_000004_create_orders_table.php';
require_once __DIR__ . '/../user/migrations/2026_02_01_000001_create_xy_table.php';

class CreateTables
{
    const DB_VERSION = '1.1.2';
    const OPTION_KEY = 'kibsterlp_db_version';

    public static function expected_tables(): array
    {
        global $wpdb;

        return [
            $wpdb->prefix . 'kib_categories',
            $wpdb->prefix . 'kib_customers',
            $wpdb->prefix . 'kib_loyalty',
            $wpdb->prefix . 'kib_orders',
            $wpdb->prefix . 'kib_shipping_addresses',
            $wpdb->prefix . 'user_details',
            $wpdb->prefix . 'kib_xy',
        ];
    }

    public static function up()
    {
        global $wpdb;
        require_once ABSPATH . 'wp-admin/includes/upgrade.php';

        CategoryMigration::up();
        CustomerMigration::up();
        LoyaltyMigration::up();
        OrderMigration::up();
        XyMigration::up();

        $charset_collate = $wpdb->get_charset_collate();
        $user_details = $wpdb->prefix . 'user_details';

        $sql_user_details = "CREATE TABLE {$user_details} (
            id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
            user_id BIGINT UNSIGNED NOT NULL,
            phone_no VARCHAR(32) NOT NULL,
            address TEXT NULL,
            zipcode VARCHAR(32) NOT NULL,
            state VARCHAR(128) NULL,
            city VARCHAR(128) NULL,
            country VARCHAR(128) NOT NULL DEFAULT '',
            created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
            updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            PRIMARY KEY (id),
            KEY user_id (user_id)
        ) {$charset_collate};";

        dbDelta($sql_user_details);

        $fk_name = 'fk_user_details_user';
        $existing_fk = $wpdb->get_results("SELECT CONSTRAINT_NAME FROM INFORMATION_SCHEMA.TABLE_CONSTRAINTS WHERE CONSTRAINT_NAME = '{$fk_name}' AND TABLE_NAME = '{$user_details}'");

        if (empty($existing_fk)) {
            $wpdb->query("ALTER TABLE {$user_details} ADD CONSTRAINT {$fk_name} FOREIGN KEY (user_id) REFERENCES {$wpdb->users}(ID) ON DELETE CASCADE");
        }

        update_option(self::OPTION_KEY, self::DB_VERSION);
    }

    public static function maybe_upgrade()
    {
        $installed = get_option(self::OPTION_KEY);
        if ($installed !== self::DB_VERSION) {
            self::up();
        }
    }

    public static function down()
    {
        global $wpdb;
        $user_details = $wpdb->prefix . 'user_details';

        $wpdb->query("SET FOREIGN_KEY_CHECKS = 0");
        OrderMigration::down();
        LoyaltyMigration::down();
        CategoryMigration::down();
        CustomerMigration::down();
        XyMigration::down();
        $wpdb->query("DROP TABLE IF EXISTS {$user_details}");
        $wpdb->query("SET FOREIGN_KEY_CHECKS = 1");

        delete_option(self::OPTION_KEY);
    }
}
