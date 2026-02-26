<?php
if (!defined('ABSPATH')) {
    exit;
}

function zabeer_auth_store_user_avatar_from_upload(array $file, $user_id)
{
    require_once ABSPATH . 'wp-admin/includes/file.php';
    require_once ABSPATH . 'wp-admin/includes/image.php';
    require_once ABSPATH . 'wp-admin/includes/media.php';

    $uploaded = wp_handle_upload($file, ['test_form' => false]);
    if (isset($uploaded['error'])) {
        return new \WP_Error('upload_failed', $uploaded['error']);
    }

    $attachment = [
        'post_mime_type' => $uploaded['type'],
        'post_title'     => sanitize_file_name(basename($uploaded['file'])),
        'post_content'   => '',
        'post_status'    => 'inherit',
    ];

    $attachment_id = wp_insert_attachment($attachment, $uploaded['file']);
    if (is_wp_error($attachment_id)) {
        return $attachment_id;
    }

    $attachment_data = wp_generate_attachment_metadata($attachment_id, $uploaded['file']);
    wp_update_attachment_metadata($attachment_id, $attachment_data);

    update_user_meta($user_id, 'zabeer_avatar_id', $attachment_id);

    return $attachment_id;
}

function zabeer_auth_get_user_from_avatar_input($id_or_email)
{
    if (is_numeric($id_or_email)) {
        return get_user_by('id', (int) $id_or_email);
    }

    if ($id_or_email instanceof WP_User) {
        return $id_or_email;
    }

    if ($id_or_email instanceof WP_Post) {
        return get_user_by('id', (int) $id_or_email->post_author);
    }

    return get_user_by('email', $id_or_email);
}

function zabeer_auth_filter_avatar_url($url, $id_or_email, $args)
{
    $user = zabeer_auth_get_user_from_avatar_input($id_or_email);
    if (!$user) {
        return $url;
    }

    $avatar_id = get_user_meta($user->ID, 'zabeer_avatar_id', true);
    if (!$avatar_id) {
        return $url;
    }

    $avatar_url = wp_get_attachment_image_url((int) $avatar_id, 'thumbnail');
    return $avatar_url ? $avatar_url : $url;
}
