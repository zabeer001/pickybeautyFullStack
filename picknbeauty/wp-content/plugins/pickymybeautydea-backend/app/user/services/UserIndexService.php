<?php
if (!defined('ABSPATH')) exit;

class UserIndexService
{
    public static function index(\WP_REST_Request $request)
    {
        $role       = sanitize_text_field($request->get_param('role')) ?: '';
        $search     = sanitize_text_field($request->get_param('search')) ?: '';
        $user_id    = absint($request->get_param('user_id'));
        $page       = (int) ($request->get_param('page') ?: 1);
        $per_page   = (int) ($request->get_param('per_page') ?: 20);

        $args = [
            'number'  => $per_page,
            'paged'   => $page,
            'orderby' => 'display_name',
            'order'   => 'ASC',
            'fields'  => ['ID', 'display_name', 'user_email'],
        ];

        if (!empty($role)) {
            $args['role'] = $role;
        }

        if (!empty($search)) {
            $args['search'] = '*' . esc_attr($search) . '*';
            $args['search_columns'] = ['user_login', 'user_email', 'display_name'];
        }

        if (!empty($user_id)) {
            $args['include'] = [$user_id];
        }

        $users = get_users($args);

        $results = [];
        foreach ($users as $user) {
            $user_obj = get_userdata($user->ID);
            $results[] = [
                'id'    => (int) $user->ID,
                'name'  => $user->display_name,
                'email' => $user->user_email,
                'role'  => $user_obj && !empty($user_obj->roles) ? $user_obj->roles[0] : null,
            ];
        }

        return new \WP_REST_Response([
            'status'  => true,
            'message' => 'Users retrieved successfully',
            'data'    => $results,
        ], 200);
    }
}
