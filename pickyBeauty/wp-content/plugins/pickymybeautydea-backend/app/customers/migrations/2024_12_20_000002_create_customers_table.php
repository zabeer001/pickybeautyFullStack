<?php

namespace Kibsterlp\App\Customers\Migrations;

if (!defined('ABSPATH')) {
    exit;
}

class CustomerMigration
{
    public static function up()
    {
        global $wpdb;
        require_once ABSPATH . 'wp-admin/includes/upgrade.php';

        $charset_collate = $wpdb->get_charset_collate();
        $table = $wpdb->prefix . 'kib_customers';

        $sql = "CREATE TABLE {$table} (
            id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
            name VARCHAR(191) NOT NULL,
            email VARCHAR(191) NOT NULL,
            phone VARCHAR(191) NULL,
            order_complete_count INT DEFAULT 0,
            order_cancelled_count INT DEFAULT 0,
            order_complete_percentage INT DEFAULT 0,
            created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
            updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            PRIMARY KEY (id),
            UNIQUE KEY email (email)
        ) {$charset_collate};";

        dbDelta($sql);
    }

    public static function down()
    {
        global $wpdb;
        $table = $wpdb->prefix . 'kib_customers';
        $wpdb->query("DROP TABLE IF EXISTS {$table}");
    }
}
