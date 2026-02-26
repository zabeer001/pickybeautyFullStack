<?php

namespace Kibsterlp\App\Orders\Migrations;

if (!defined('ABSPATH')) {
    exit;
}

class OrderMigration
{
    public static function up()
    {
        global $wpdb;
        require_once ABSPATH . 'wp-admin/includes/upgrade.php';

        $charset_collate = $wpdb->get_charset_collate();
        $orders_table = $wpdb->prefix . 'kib_orders';
        $shipping_table = $wpdb->prefix . 'kib_shipping_addresses';
        $categories_table = $wpdb->prefix . 'kib_categories';

        $customers_table = $wpdb->prefix . 'kib_customers';
        $sql_orders = "CREATE TABLE {$orders_table} (
            id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
            user_id BIGINT UNSIGNED NULL,
            customer_id BIGINT UNSIGNED NULL,
            vendor_id BIGINT UNSIGNED NULL,
            x DECIMAL(10,6) NULL,
            y DECIMAL(10,6) NULL,
            price DECIMAL(12,2) NOT NULL DEFAULT 0.00,
            shipping_id BIGINT UNSIGNED NULL,
            budget DECIMAL(12,2) NULL,
            order_title VARCHAR(255) NULL,
            order_unique_id VARCHAR(191) NULL,
            order_details TEXT NULL,
            payment_method VARCHAR(32) NULL,

            sharing_status VARCHAR(32) NOT NULL DEFAULT 'not accepted',
            created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
            updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            payment_status VARCHAR(32) NOT NULL DEFAULT 'pending',
            category_id BIGINT UNSIGNED NULL,
            PRIMARY KEY (id),
            UNIQUE KEY order_unique_id (order_unique_id),
            KEY user_id (user_id),
            KEY customer_id (customer_id),
            KEY vendor_id (vendor_id),
            KEY shipping_id (shipping_id),
            KEY category_id (category_id),
            KEY x (x),
            KEY y (y),
            CONSTRAINT fk_orders_category FOREIGN KEY (category_id) REFERENCES {$categories_table}(id) ON DELETE SET NULL ON UPDATE CASCADE,
            CONSTRAINT fk_orders_customer FOREIGN KEY (customer_id) REFERENCES {$customers_table}(id) ON DELETE SET NULL ON UPDATE CASCADE
        ) {$charset_collate};";

        $sql_shipping = "CREATE TABLE {$shipping_table} (
            id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
            email VARCHAR(191) NULL,
            phone VARCHAR(64) NULL,
            name VARCHAR(191) NULL,
            country VARCHAR(128) NULL,
            city VARCHAR(128) NULL,
            district VARCHAR(128) NULL,
            zip_code VARCHAR(32) NULL,
            created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
            updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            PRIMARY KEY (id),
            KEY email (email)
        ) {$charset_collate};";

        dbDelta($sql_shipping);
        dbDelta($sql_orders);
    }

    public static function down()
    {
        global $wpdb;
        $orders_table = $wpdb->prefix . 'kib_orders';
        $shipping_table = $wpdb->prefix . 'kib_shipping_addresses';

        $wpdb->query("DROP TABLE IF EXISTS {$orders_table}");
        $wpdb->query("DROP TABLE IF EXISTS {$shipping_table}");
    }
}
