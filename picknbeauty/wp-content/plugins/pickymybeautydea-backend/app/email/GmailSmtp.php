<?php
/**
 * Plugin Name: Gmail SMTP (Must-Use)
 * Description: Routes wp_mail through Gmail SMTP using wp-config constants.
 */

if (!defined('ABSPATH')) {
	exit;
}

add_action('phpmailer_init', function ($phpmailer) {
	if (!defined('SMTP_HOST') || SMTP_HOST === '') {
		return;
	}

	$phpmailer->isSMTP();
	$phpmailer->Host = SMTP_HOST;
	$phpmailer->Port = defined('SMTP_PORT') ? SMTP_PORT : 587;
	$phpmailer->SMTPAuth = true;
	$phpmailer->Username = defined('SMTP_USERNAME') ? SMTP_USERNAME : '';
	$phpmailer->Password = defined('SMTP_PASSWORD') ? SMTP_PASSWORD : '';
	$phpmailer->SMTPSecure = defined('SMTP_SECURE') ? SMTP_SECURE : 'tls';
	$phpmailer->From = defined('SMTP_FROM_EMAIL') ? SMTP_FROM_EMAIL : $phpmailer->From;
	$phpmailer->FromName = defined('SMTP_FROM_NAME') ? SMTP_FROM_NAME : $phpmailer->FromName;
	$phpmailer->SMTPAutoTLS = true;
});

add_filter('wp_mail_from', function ($from) {
	if (defined('SMTP_FROM_EMAIL') && SMTP_FROM_EMAIL !== '') {
		return SMTP_FROM_EMAIL;
	}
	return $from;
});

add_filter('wp_mail_from_name', function ($name) {
	if (defined('SMTP_FROM_NAME') && SMTP_FROM_NAME !== '') {
		return SMTP_FROM_NAME;
	}
	return $name;
});
