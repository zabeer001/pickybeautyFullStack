<?php
// zip-style: procedural, tiny
if (!defined('ABSPATH')) { exit; }

use Kibsterlp\App\Migrations\CreateTables;
use Kibsterlp\App\User\Migrations\XyMigration;

/**
 * Register migrations in execution order.
 *
 * Add new migrations by appending a new ID => callable pair.
 * Never change existing IDs once shipped.
 */
function kibsterlp_get_migrations(): array {
    return [
        '20241220_000001_create_tables' => function () {
            if (class_exists(CreateTables::class)) {
                CreateTables::up();
            }
        },
        '20260201_000001_create_xy_table' => function () {
            if (class_exists(XyMigration::class)) {
                XyMigration::up();
            }
        },
        '20260212_000001_add_payment_method' => function () {
            if (class_exists(CreateTables::class)) {
                CreateTables::up();
            }
        },
    ];
}

/**
 * Run any pending migrations (idempotent).
 */
function kibsterlp_run_migrations(): void {
    $migrations = kibsterlp_get_migrations();
    if (empty($migrations) || !is_array($migrations)) {
        return;
    }

    ksort($migrations);

    $applied = get_option('kibsterlp_migrations', []);
    if (!is_array($applied)) {
        $applied = [];
    }

    $changed = false;
    foreach ($migrations as $id => $callback) {
        if (in_array($id, $applied, true)) {
            continue;
        }
        if (is_callable($callback)) {
            $callback();
            $applied[] = $id;
            $changed = true;
        }
    }

    if ($changed) {
        update_option('kibsterlp_migrations', $applied, false);
    }
}

// Run migrations on load so new files apply without deactivation.
add_action('plugins_loaded', 'kibsterlp_run_migrations');
