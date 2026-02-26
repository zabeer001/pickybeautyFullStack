<?php

namespace Kibsterlp\App\Loyalty\Migrations;

if (!defined('ABSPATH')) {
    exit;
}

class LoyaltyMigration
{
    public static function up()
    {
        global $wpdb;
        require_once ABSPATH . 'wp-admin/includes/upgrade.php';

        $charset_collate = $wpdb->get_charset_collate();
        $table = $wpdb->prefix . 'kib_loyalty';

        $sql = "CREATE TABLE {$table} (
            id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
            min_order INT NOT NULL DEFAULT 0,
            max_order INT NOT NULL DEFAULT 0,
            order_complete_percentage INT NOT NULL DEFAULT 0,
            discount INT NOT NULL DEFAULT 0,
            status VARCHAR(32) NOT NULL DEFAULT 'active',
            created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
            updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            PRIMARY KEY (id)
        ) {$charset_collate};";

        dbDelta($sql);
    }

    public static function down()
    {
        global $wpdb;
        $table = $wpdb->prefix . 'kib_loyalty';
        $wpdb->query("DROP TABLE IF EXISTS {$table}");
    }
}
