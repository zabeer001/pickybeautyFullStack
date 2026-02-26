<?php

if (!defined('ABSPATH')) {
    exit;
}

use Kibsterlp\App\Migrations\CreateTables;

function kibsterlp_register_table_health_hooks(): void
{
    add_action('admin_notices', 'kibsterlp_tables_missing_notice');
    add_action('admin_notices', 'kibsterlp_tables_ok_notice');
}

function kibsterlp_ensure_tables(): void
{
    if (!class_exists(CreateTables::class)) {
        return;
    }

    global $wpdb;
    $expected = CreateTables::expected_tables();
    $missing = [];

    foreach ($expected as $table) {
        $exists = $wpdb->get_var($wpdb->prepare('SHOW TABLES LIKE %s', $table));
        if ($exists !== $table) {
            $missing[] = $table;
        }
    }

    if (!empty($missing)) {
        CreateTables::up();

        $still_missing = [];
        foreach ($missing as $table) {
            $exists = $wpdb->get_var($wpdb->prepare('SHOW TABLES LIKE %s', $table));
            if ($exists !== $table) {
                $still_missing[] = $table;
            }
        }

        if (!empty($still_missing)) {
            update_option('kibsterlp_tables_missing', $still_missing, false);
            delete_option('kibsterlp_tables_ok');
        } else {
            delete_option('kibsterlp_tables_missing');
            update_option('kibsterlp_tables_ok', 1, false);
        }
    } else {
        delete_option('kibsterlp_tables_missing');
        update_option('kibsterlp_tables_ok', 1, false);
    }
}

function kibsterlp_tables_missing_notice(): void
{
    $missing = get_option('kibsterlp_tables_missing', []);
    if (empty($missing) || !is_array($missing)) {
        return;
    }

    $safe_missing = array_map('esc_html', $missing);
    echo '<div class="notice notice-error"><p><strong>KibsterLP:</strong> Missing tables: ' . implode(', ', $safe_missing) . '.</p></div>';
}

function kibsterlp_tables_ok_notice(): void
{
    $ok = get_option('kibsterlp_tables_ok', 0);
    if (!$ok) {
        return;
    }
    delete_option('kibsterlp_tables_ok');
    echo '<div class="notice notice-success"><p><strong>KibsterLP:</strong> All plugin tables are created.</p></div>';
}
