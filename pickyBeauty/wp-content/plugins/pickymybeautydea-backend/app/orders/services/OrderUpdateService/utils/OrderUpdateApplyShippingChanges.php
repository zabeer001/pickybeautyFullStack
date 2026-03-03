<?php
if (!defined('ABSPATH')) exit;

class OrderUpdateApplyShippingChanges
{
    public static function run($wpdb, string $orders_table, int $id, ?array $shipping, array $payload): array
    {
        if (!$shipping) {
            return $payload;
        }

        $data = $payload['data'];
        $types = $payload['types'];

        $current_shipping_id = null;
        if (!empty($data['shipping_id'])) {
            $current_shipping_id = (int) $data['shipping_id'];
        } else {
            $current_shipping_id = (int) $wpdb->get_var(
                $wpdb->prepare("SELECT shipping_id FROM {$orders_table} WHERE id = %d", $id)
            );
        }

        if ($current_shipping_id) {
            $table = $wpdb->prefix . 'kib_shipping_addresses';
            $shipping_data = [];
            $shipping_types = [];

            foreach (['email', 'phone', 'name', 'country', 'city', 'district', 'zip_code'] as $field) {
                if (array_key_exists($field, $shipping) && $shipping[$field] !== null) {
                    $shipping_data[$field] = $shipping[$field];
                    $shipping_types[] = '%s';
                }
            }

            if (!empty($shipping_data)) {
                $shipping_data['updated_at'] = current_time('mysql');
                $shipping_types[] = '%s';

                $ok = $wpdb->update($table, $shipping_data, ['id' => $current_shipping_id], $shipping_types, ['%d']);
                if ($ok === false) {
                    return ['error' => new \WP_Error('kib_shipping_update_failed', 'Failed to update shipping address', ['status' => 500])];
                }
            }

            return [
                'data' => $data,
                'types' => $types,
            ];
        }

        $new_shipping_id = OrderUpdateInsertShipping::run($shipping);
        if (is_wp_error($new_shipping_id)) {
            return ['error' => $new_shipping_id];
        }

        $data['shipping_id'] = (int) $new_shipping_id;
        $types[] = '%d';

        return [
            'data' => $data,
            'types' => $types,
        ];
    }
}
