<?php

namespace Kibsterlp\App\Categories\Migrations;

if (!defined('ABSPATH')) {
    exit;
}

class CategoryMigration
{
    public static function up()
    {
        global $wpdb;
        require_once ABSPATH . 'wp-admin/includes/upgrade.php';

        $charset_collate = $wpdb->get_charset_collate();
        $table = $wpdb->prefix . 'kib_categories';

        $sql = "CREATE TABLE {$table} (
            id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
            title VARCHAR(191) NULL,
            description TEXT NULL,
            created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
            updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            PRIMARY KEY (id),
            KEY title (title)
        ) {$charset_collate};";

        dbDelta($sql);
    }

    public static function down()
    {
        global $wpdb;
        $table = $wpdb->prefix . 'kib_categories';
        $wpdb->query("DROP TABLE IF EXISTS {$table}");
    }
}
