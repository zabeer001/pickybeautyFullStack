<?php
if (!defined('ABSPATH')) exit;

class OrderCreateBuildOrderEmailHtml
{
    public static function run(int $order_id, string $order_unique_id, ?string $order_title, ?array $shipping): string
    {
        $site_name = wp_specialchars_decode(get_bloginfo('name'), ENT_QUOTES);
        $safe_title = $order_title ? esc_html($order_title) : 'Your order';
        $name = isset($shipping['name']) && $shipping['name'] ? esc_html($shipping['name']) : 'there';
        $home_url = esc_url(home_url('/'));

        return '
            <div style="font-family: Arial, sans-serif; color: #1f2937; line-height: 1.6;">
                <h2 style="color:#111827; margin-bottom: 8px;">Order confirmed</h2>
                <p>Hi ' . $name . ',</p>
                <p>Thanks for your order with <strong>' . esc_html($site_name) . '</strong>. We have received it and it is confirmed.</p>
                <p><strong>Order:</strong> ' . $safe_title . '<br/>
                   <strong>Order ID:</strong> ' . esc_html((string) $order_id) . '</p>
                <p>We will contact you if we need any more details.</p>
                <p style="margin-top: 24px;">Thanks,<br/>' . esc_html($site_name) . ' Team</p>
                <hr style="border:none;border-top:1px solid #e5e7eb;margin:24px 0;">
                <p style="font-size: 12px; color: #6b7280;">Visit us: <a href="' . $home_url . '">' . $home_url . '</a></p>
            </div>
        ';
    }
}
