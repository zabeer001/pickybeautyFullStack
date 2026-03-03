<?php
if (!defined('ABSPATH')) exit;

class OrderCreateSendConfirmationEmail
{
    public static function run(int $order_id, string $order_unique_id, ?string $order_title, ?array $shipping, int $user_id): array
    {
        $to = OrderCreateResolveEmailRecipient::run($shipping, $user_id);
        if ($to === '') {
            return [
                'sent' => false,
                'reason' => 'No recipient email found.',
            ];
        }

        $site_name = wp_specialchars_decode(get_bloginfo('name'), ENT_QUOTES);
        $subject = sprintf('Your order is confirmed - %s', $site_name);
        $message = OrderCreateBuildOrderEmailHtml::run($order_id, $order_unique_id, $order_title, $shipping);

        $error_data = null;
        $error_handler = function ($wp_error) use (&$error_data) {
            $error_data = $wp_error;
        };
        add_action('wp_mail_failed', $error_handler);

        $sent = wp_mail($to, $subject, $message, ['Content-Type: text/html; charset=UTF-8']);

        remove_action('wp_mail_failed', $error_handler);

        if (!$sent) {
            $reason = 'Unknown error.';
            $details = null;
            if ($error_data instanceof \WP_Error) {
                $reason = $error_data->get_error_message();
                $details = $error_data->get_error_data();
            }

            return [
                'sent' => false,
                'reason' => $reason,
                'details' => $details,
                'to' => $to,
            ];
        }

        return [
            'sent' => true,
            'to' => $to,
        ];
    }
}
