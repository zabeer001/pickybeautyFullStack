<?php
if (!defined('ABSPATH')) exit;

class UserMeService
{
    public static function me(\WP_REST_Request $request)
    {
        $user = wp_get_current_user();

        if ($user->ID === 0) {
            return new \WP_Error(
                'not_logged_in',
                __('User not authenticated', 'kibsterlp'),
                ['status' => 401]
            );
        }

        $userData = [
            'id'          => $user->ID,
            'username'    => $user->user_login,
            'email'       => $user->user_email,
            'name'        => $user->display_name,
            'roles'       => $user->roles,
            'registered'  => $user->user_registered,
        ];

        $response = [
            'status'  => true,
            'message' => __('User retrieved successfully', 'kibsterlp'),
            'data'    => $userData,
        ];

        return rest_ensure_response($response);
    }
}
