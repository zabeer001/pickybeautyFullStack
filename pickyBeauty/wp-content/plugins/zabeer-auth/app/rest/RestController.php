<?php
if (!defined('ABSPATH')) {
    exit;
}

class ZabeerAuthRestController
{
    public function register_routes(): void
    {
        register_rest_route('zabeer-auth/v1', '/me', [
            'methods'  => 'GET',
            'callback' => [$this, 'get_current_user'],
            'permission_callback' => function () {
                return is_user_logged_in();
            },
        ]);

        register_rest_route('zabeer-auth/v1', '/register', [
            'methods'             => 'POST',
            'callback'            => [$this, 'handle_vendor_register'],
            'permission_callback' => '__return_true',
        ]);
    }

    public function get_current_user(\WP_REST_Request $request)
    {
        $user = wp_get_current_user();

        if (!$user || 0 === $user->ID) {
            return new \WP_Error('not_logged_in', 'You are not authenticated.', ['status' => 401]);
        }

        $data = [
            'id'            => $user->ID,
            'username'      => $user->user_login,
            'email'         => $user->user_email,
            'display_name'  => $user->display_name,
            'roles'         => $user->roles,
            'first_name'    => $user->first_name,
            'last_name'     => $user->last_name,
            'registered_at' => $user->user_registered,
            'meta'          => get_user_meta($user->ID),
        ];

        return rest_ensure_response($data);
    }

    public function handle_vendor_register(\WP_REST_Request $request)
    {
        $username   = sanitize_user($request->get_param('username'));
        $email      = sanitize_email($request->get_param('email'));
        $password   = $request->get_param('password');
        $confirm    = $request->get_param('confirm_password');
        $first_name = sanitize_text_field($request->get_param('first_name'));
        $last_name  = sanitize_text_field($request->get_param('last_name'));
        $address    = sanitize_text_field($request->get_param('address'));
        $postal_raw = sanitize_text_field($request->get_param('postal_code'));
        $city       = sanitize_text_field($request->get_param('city'));
        $phone      = sanitize_text_field($request->get_param('phone'));
        $description = sanitize_textarea_field($request->get_param('description'));

        $targets_param = $request->get_param('targets');
        if (is_array($targets_param)) {
            $targets = implode(', ', array_map('sanitize_text_field', $targets_param));
        } else {
            $targets = sanitize_text_field($targets_param);
        }

        $postal = $postal_raw;
        if (empty($city) && !empty($postal_raw)) {
            if (preg_match('/^\\s*([\\d]{3,6})\\s+(.*)$/u', $postal_raw, $matches)) {
                $postal = sanitize_text_field($matches[1]);
                $city   = sanitize_text_field($matches[2]);
            }
        }

        if (empty($username) || empty($email) || empty($password) || empty($confirm)) {
            return new \WP_Error('missing_fields', 'Please fill in all required fields.', ['status' => 400]);
        }

        if ($password !== $confirm) {
            return new \WP_Error('password_mismatch', 'Passwords do not match.', ['status' => 400]);
        }

        if (username_exists($username)) {
            return new \WP_Error('username_exists', 'Username already exists.', ['status' => 400]);
        }

        if (email_exists($email)) {
            return new \WP_Error('email_exists', 'Email already registered.', ['status' => 400]);
        }

        $files = $request->get_file_params();

        $user_id = wp_create_user($username, $password, $email);

        if (is_wp_error($user_id)) {
            return new \WP_Error('registration_failed', $user_id->get_error_message(), ['status' => 400]);
        }

        $user = new \WP_User($user_id);
        $user->set_role('vendor');

        update_user_meta($user_id, 'is_vendor', true);
        update_user_meta($user_id, 'first_name', $first_name);
        update_user_meta($user_id, 'last_name', $last_name);
        update_user_meta($user_id, 'vendor_address', $address);
        update_user_meta($user_id, 'zipcode', $postal);
        update_user_meta($user_id, 'vendor_city', $city);
        update_user_meta($user_id, 'vendor_phone', $phone);
        update_user_meta($user_id, 'vendor_description', $description);
        if (!empty($targets)) {
            update_user_meta($user_id, 'vendor_targets', $targets);
        }
        if (!empty($files['vendor_logo']['name'])) {
            $avatar_id = zabeer_auth_store_user_avatar_from_upload($files['vendor_logo'], $user_id);
            if (!is_wp_error($avatar_id)) {
                $logo_url = wp_get_attachment_url($avatar_id);
                if ($logo_url) {
                    update_user_meta($user_id, 'vendor_logo_url', esc_url_raw($logo_url));
                }
            }
        }

        zabeer_auth_send_welcome_email($user_id, 'vendor');

        return rest_ensure_response([
            'success' => true,
            'message' => 'Registration successful.',
            'user_id' => $user_id,
        ]);
    }
}
