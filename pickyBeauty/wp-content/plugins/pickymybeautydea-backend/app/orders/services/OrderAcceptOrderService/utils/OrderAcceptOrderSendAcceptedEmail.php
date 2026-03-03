<?php
if (!defined('ABSPATH')) exit;

require_once __DIR__ . '/OrderAcceptOrderResolveEmailRecipient.php';
require_once __DIR__ . '/OrderAcceptOrderBuildEmailHtml.php';

class OrderAcceptOrderSendAcceptedEmail
{
    public static function run(
        int $order_id,
        string $order_unique_id,
        ?string $order_title,
        ?string $shipping_email,
        ?string $shipping_name,
        int $user_id
    ): array {
        $recipient = OrderAcceptOrderResolveEmailRecipient::run($shipping_email, $shipping_name, $user_id);

        if ($recipient['to'] === '') {
            return [
                'sent' => false,
                'reason' => 'No recipient email found.',
            ];
        }

        $site_name = wp_specialchars_decode(get_bloginfo('name'), ENT_QUOTES);
        $subject = sprintf('Your order is accepted - %s', $site_name);
        $message = OrderAcceptOrderBuildEmailHtml::run(
            $order_id,
            $order_unique_id,
            $order_title,
            $recipient['name']
        );

        $error_data = null;
        $error_handler = function ($wp_error) use (&$error_data) {
            $error_data = $wp_error;
        };
        add_action('wp_mail_failed', $error_handler);

        $sent = wp_mail($recipient['to'], $subject, $message, ['Content-Type: text/html; charset=UTF-8']);

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
                'to' => $recipient['to'],
            ];
        }

        return [
            'sent' => true,
            'to' => $recipient['to'],
        ];
    }
}
