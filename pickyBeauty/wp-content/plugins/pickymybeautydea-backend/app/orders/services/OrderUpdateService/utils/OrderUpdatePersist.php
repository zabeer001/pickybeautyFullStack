<?php
if (!defined('ABSPATH')) exit;

class OrderUpdatePersist
{
    public static function run($wpdb, string $orders_table, int $id, array $data, array $types)
    {
        $data['updated_at'] = current_time('mysql');
        $types[] = '%s';

        return $wpdb->update($orders_table, $data, ['id' => $id], $types, ['%d']);
    }
}
