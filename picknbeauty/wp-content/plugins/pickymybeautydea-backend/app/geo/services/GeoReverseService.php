<?php
if (!defined('ABSPATH')) exit;

class GeoReverseService
{
    public static function reverse(\WP_REST_Request $request)
    {
        $lat = $request->get_param('lat');
        $lon = $request->get_param('lon');

        if ($lat === null || $lon === null) {
            return new \WP_Error('geo_missing_params', 'Missing lat/lon.', ['status' => 400]);
        }

        $url = add_query_arg([
            'format' => 'json',
            'lat' => $lat,
            'lon' => $lon,
        ], 'https://nominatim.openstreetmap.org/reverse');

        $response = wp_remote_get($url, [
            'timeout' => 15,
            'headers' => [
                'User-Agent' => 'kibsterlp/1.0 (contact: admin@pickmybeauty.de)',
            ],
        ]);

        if (is_wp_error($response)) {
            return new \WP_Error('geo_request_failed', $response->get_error_message(), ['status' => 500]);
        }

        $status = wp_remote_retrieve_response_code($response);
        $body = json_decode(wp_remote_retrieve_body($response), true);

        if ($status < 200 || $status >= 300 || !is_array($body)) {
            return new \WP_Error('geo_bad_response', 'Failed to reverse geocode.', ['status' => 502]);
        }

        return new \WP_REST_Response($body, 200);
    }
}
