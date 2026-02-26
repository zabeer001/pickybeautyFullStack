<?php

namespace Kibsterlp\App\User\Migrations;

if (!defined('ABSPATH')) {
    exit;
}

class XyMigration
{
    public static function up()
    {
        global $wpdb;
        require_once ABSPATH . 'wp-admin/includes/upgrade.php';

        $charset_collate = $wpdb->get_charset_collate();
        $table = $wpdb->prefix . 'kib_xy';

        $sql = "CREATE TABLE {$table} (
            id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
            user_id BIGINT UNSIGNED NOT NULL,
            x DECIMAL(10,6) NOT NULL DEFAULT 0,
            y DECIMAL(10,6) NOT NULL DEFAULT 0,
            status VARCHAR(32) NOT NULL DEFAULT '',
            radius INT NOT NULL DEFAULT 0,
            full_address TEXT NULL,
            created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
            updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            PRIMARY KEY (id),
            KEY user_id (user_id),
            KEY x (x),
            KEY y (y)
        ) {$charset_collate};";

        dbDelta($sql);

        $fk_name = 'fk_xy_user';
        $existing_fk = $wpdb->get_results("SELECT CONSTRAINT_NAME FROM INFORMATION_SCHEMA.TABLE_CONSTRAINTS WHERE CONSTRAINT_NAME = '{$fk_name}' AND TABLE_NAME = '{$table}'");

        if (empty($existing_fk)) {
            $wpdb->query("ALTER TABLE {$table} ADD CONSTRAINT {$fk_name} FOREIGN KEY (user_id) REFERENCES {$wpdb->users}(ID) ON DELETE CASCADE");
        }
    }

    public static function down()
    {
        global $wpdb;
        $table = $wpdb->prefix . 'kib_xy';
        $wpdb->query("DROP TABLE IF EXISTS {$table}");
    }
}
