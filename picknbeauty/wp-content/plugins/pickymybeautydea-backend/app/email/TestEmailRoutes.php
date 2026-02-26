<?php
if (!defined('ABSPATH')) {
    exit;
}

class TestEmailRoutes
{
    private function get_from_email(): string
    {
        if (defined('SMTP_FROM_EMAIL') && SMTP_FROM_EMAIL !== '') {
            return SMTP_FROM_EMAIL;
        }

        return get_option('admin_email');
    }

    private function get_from_name(): string
    {
        if (defined('SMTP_FROM_NAME') && SMTP_FROM_NAME !== '') {
            return SMTP_FROM_NAME;
        }

        $name = get_bloginfo('name');
        return $name ?: 'WordPress';
    }

    public function register_routes(): void
    {
        // register_rest_route('kibsterlp-admin/v1', '/test-email', [
        //     'methods'  => 'GET',
        //     'callback' => [$this, 'send_test_email'],
        //     'permission_callback' => '__return_true',
        // ]);
    }

    public function send_test_email(\WP_REST_Request $request): \WP_REST_Response
    {
        $to = get_option('admin_email');
        $from_email = $this->get_from_email();
        $from_name = $this->get_from_name();
        $subject = 'KibsterLP test email';
        $message = 'This is a test email from KibsterLP.';

        if (!is_email($from_email)) {
            return new \WP_REST_Response([
                'success' => false,
                'message' => 'Email failed to send.',
                'reason' => 'Invalid or empty From email.',
                'details' => [
                    'from_email' => $from_email,
                    'from_name' => $from_name,
                    'admin_email' => $to,
                ],
            ], 500);
        }

        $from_email_filter = function () use ($from_email) {
            return $from_email;
        };
        $from_name_filter = function () use ($from_name) {
            return $from_name;
        };
        add_filter('wp_mail_from', $from_email_filter);
        add_filter('wp_mail_from_name', $from_name_filter);

        $error_data = null;
        $error_handler = function ($wp_error) use (&$error_data) {
            $error_data = $wp_error;
        };
        add_action('wp_mail_failed', $error_handler);

        $sent = wp_mail($to, $subject, $message);

        remove_filter('wp_mail_from', $from_email_filter);
        remove_filter('wp_mail_from_name', $from_name_filter);
        remove_action('wp_mail_failed', $error_handler);

        if (!$sent) {
            $reason = 'Unknown error.';
            $data = null;
            if ($error_data instanceof \WP_Error) {
                $reason = $error_data->get_error_message();
                $data = $error_data->get_error_data();
            }
            return new \WP_REST_Response([
                'success' => false,
                'message' => 'Email failed to send.',
                'reason' => $reason,
                'details' => $data,
            ], 500);
        }

        return new \WP_REST_Response([
            'success' => true,
            'message' => 'Email sent successfully.',
            'to' => $to,
        ], 200);
    }
}
