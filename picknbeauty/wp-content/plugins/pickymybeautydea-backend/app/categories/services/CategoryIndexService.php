<?php
if (!defined('ABSPATH')) exit;

class CategoryIndexService
{
    /**
     * GET /categories
     * List all categories (paginated)
     */
    public static function index(\WP_REST_Request $request)
    {
        global $wpdb;
        $table = $wpdb->prefix . 'kib_categories';

        $per_page = max(1, (int) $request->get_param('per_page') ?: 20);
        $page     = max(1, (int) $request->get_param('page') ?: 1);
        $offset   = ($page - 1) * $per_page;

        $items = $wpdb->get_results(
            $wpdb->prepare("SELECT * FROM {$table} LIMIT %d OFFSET %d", $per_page, $offset),
            ARRAY_A
        );

        $total = (int) $wpdb->get_var("SELECT COUNT(*) FROM {$table}");

        return new \WP_REST_Response([
            'data'  => $items,
            'meta'  => [
                'total'     => $total,
                'page'      => $page,
                'per_page'  => $per_page,
            ],
        ], 200);
    }
}
