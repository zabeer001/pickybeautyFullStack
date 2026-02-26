<?php
if (!defined('ABSPATH')) {
    exit;
}

function zabeer_auth_send_welcome_email(int $user_id, string $role): void
{
    $user = get_user_by('id', $user_id);
    if (!$user) {
        return;
    }

    $site_name = wp_specialchars_decode(get_bloginfo('name'), ENT_QUOTES);
    $login_url = wp_login_url();
    $first_name = get_user_meta($user_id, 'first_name', true);
    $name = $first_name ?: $user->display_name;

    if ($role === 'vendor') {
        $subject = sprintf('Welcome to %s as a vendor', $site_name);
        $message = sprintf(
            "Hi %s,\n\nThanks for joining %s as a vendor. Your account is ready and you can log in here:\n%s\n\nIf you have questions, just reply to this email.\n\nThanks,\n%s Team",
            $name,
            $site_name,
            $login_url,
            $site_name
        );
    } else {
        $subject = sprintf('Welcome to %s', $site_name);
        $message = sprintf(
            "Hi %s,\n\nWelcome to %s. Your account has been created successfully.\nYou can log in here:\n%s\n\nWe are happy to have you with us.\n\nThanks,\n%s Team",
            $name,
            $site_name,
            $login_url,
            $site_name
        );
    }

    wp_mail($user->user_email, $subject, $message);
}
