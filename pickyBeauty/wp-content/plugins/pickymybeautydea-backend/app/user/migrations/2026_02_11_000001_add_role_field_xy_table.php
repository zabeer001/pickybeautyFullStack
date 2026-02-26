<?php

namespace Kibsterlp\App\User\Migrations;

if (!defined('ABSPATH')) {
    exit;
}

class AddRoleFieldXyTableMigration
{
    public static function up()
    {
        global $wpdb;
        $table = $wpdb->prefix . 'kib_xy';

        $wpdb->query("ALTER TABLE {$table} ADD COLUMN role VARCHAR(32) NULL AFTER user_id");
    }

    public static function down()
    {
        global $wpdb;
        $table = $wpdb->prefix . 'kib_xy';

        $wpdb->query("ALTER TABLE {$table} DROP COLUMN role");
    }
}
